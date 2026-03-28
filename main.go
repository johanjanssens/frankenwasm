package main

import (
	"context"
	"log/slog"
	"net/http"
	"os"
	"os/signal"
	"path/filepath"
	"strconv"
	"strings"
	"syscall"
	"time"

	_ "github.com/johanjanssens/frankenwasm/phpext" // registers PHP extension via init()
	"github.com/johanjanssens/frankenwasm/wasm"

	"github.com/dunglas/frankenphp"
	"github.com/joho/godotenv"
	"github.com/lmittmann/tint"
)

func main() {
	// Load .env if present
	_ = godotenv.Load()

	// Set up logger
	logger := slog.New(tint.NewHandler(os.Stdout, &tint.Options{
		Level:      slog.LevelDebug,
		TimeFormat: time.Kitchen,
	}))
	slog.SetDefault(logger)

	ctx, cancel := signal.NotifyContext(context.Background(), syscall.SIGINT, syscall.SIGTERM)
	defer cancel()

	// Discover .wasm files in plugins/ directory
	pluginDir := "plugins"
	if dir := os.Getenv("FRANKENWASM_PLUGIN_DIR"); dir != "" {
		pluginDir = dir
	}

	wasmFiles, err := filepath.Glob(filepath.Join(pluginDir, "*.wasm"))
	if err != nil {
		logger.Error("Failed to discover plugins", "error", err)
		os.Exit(1)
	}

	// Create plugin manager
	manager, err := wasm.NewManager(
		wasm.WithLogger(logger.Handler()),
		wasm.WithCachePath(filepath.Join(os.TempDir(), "frankenwasm-cache")),
	)
	if err != nil {
		logger.Error("Failed to create plugin manager", "error", err)
		os.Exit(1)
	}
	defer manager.Close(ctx)

	// Load all discovered plugins
	for _, wasmFile := range wasmFiles {
		name := strings.TrimSuffix(filepath.Base(wasmFile), ".wasm")
		absPath, err := filepath.Abs(wasmFile)
		if err != nil {
			logger.Error("Failed to resolve plugin path", "file", wasmFile, "error", err)
			continue
		}

		if err := manager.Load(ctx, name, absPath); err != nil {
			logger.Error("Failed to load plugin", "name", name, "path", absPath, "error", err)
			continue
		}
	}

	if !manager.IsLoaded() {
		logger.Warn("No plugins loaded", "dir", pluginDir)
	}

	// Create a shared registry (will be cloned per request)
	baseRegistry, err := manager.InstantiateAll(ctx)
	if err != nil {
		logger.Error("Failed to instantiate plugins", "error", err)
		os.Exit(1)
	}
	defer baseRegistry.Close(ctx)

	// Resolve document root
	docRootDir := "examples"
	if dir := os.Getenv("FRANKENWASM_DOC_ROOT"); dir != "" {
		docRootDir = dir
	}
	docRoot, err := filepath.Abs(docRootDir)
	if err != nil {
		logger.Error("Failed to resolve document root", "error", err)
		os.Exit(1)
	}

	// Init FrankenPHP
	numThreads := 2
	if n, err := strconv.Atoi(os.Getenv("FRANKENWASM_THREADS")); err == nil && n > 0 {
		numThreads = n
	}

	initOptions := []frankenphp.Option{
		frankenphp.WithNumThreads(numThreads),
		frankenphp.WithLogger(logger),
		frankenphp.WithPhpIni(map[string]string{
			"include_path": docRoot,
		}),
	}

	if err := frankenphp.Init(initOptions...); err != nil {
		logger.Error("Failed to initialize FrankenPHP", "error", err)
		os.Exit(1)
	}
	defer frankenphp.Shutdown()

	// Set up HTTP handler
	addr := ":8080"
	if port := os.Getenv("FRANKENWASM_PORT"); port != "" {
		addr = ":" + port
	}

	mux := http.NewServeMux()
	mux.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		// Rewrite directory requests to index.php
		if r.URL.Path == "/" || strings.HasSuffix(r.URL.Path, "/") {
			r.URL.Path = r.URL.Path + "index.php"
		}

		// Clone registry for this request
		registry, err := baseRegistry.Clone(r.Context())
		if err != nil {
			logger.Error("Failed to clone registry", "error", err)
			http.Error(w, "Internal server error", http.StatusInternalServerError)
			return
		}
		defer registry.Close(r.Context())

		// Add registry to context
		ctx := wasm.WithContext(r.Context(), registry)
		r = r.WithContext(ctx)

		// Create FrankenPHP request
		req, err := frankenphp.NewRequestWithContext(r,
			frankenphp.WithRequestResolvedDocumentRoot(docRoot),
			frankenphp.WithRequestLogger(logger),
		)
		if err != nil {
			logger.Error("Failed to create FrankenPHP request", "error", err)
			http.Error(w, "Internal server error", http.StatusInternalServerError)
			return
		}

		if err := frankenphp.ServeHTTP(w, req); err != nil {
			logger.Error("Failed to serve PHP", "error", err)
		}
	})

	server := &http.Server{
		Addr:         addr,
		Handler:      mux,
		ReadTimeout:  30 * time.Second,
		WriteTimeout: 120 * time.Second,
		IdleTimeout:  60 * time.Second,
	}

	// Start server in goroutine
	go func() {
		logger.Info("Starting FrankenWASM server", "addr", addr, "docroot", docRoot, "plugins", len(wasmFiles))
		if err := server.ListenAndServe(); err != nil && err != http.ErrServerClosed {
			logger.Error("Server error", "error", err)
			cancel()
		}
	}()

	// Wait for shutdown signal
	<-ctx.Done()
	logger.Info("Shutting down...")

	shutdownCtx, shutdownCancel := context.WithTimeout(context.Background(), 10*time.Second)
	defer shutdownCancel()
	if err := server.Shutdown(shutdownCtx); err != nil {
		logger.Error("Failed to shutdown server", "error", err)
	}
}
