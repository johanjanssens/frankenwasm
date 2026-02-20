package wasm

import (
	"log/slog"

	extism "github.com/extism/go-sdk"
)

type ManagerOption func(*Manager)

// WithCachePath sets the directory path for the WebAssembly compilation cache.
func WithCachePath(cachePath string) ManagerOption {
	return func(m *Manager) {
		m.cachePath = cachePath
	}
}

// WithLogger sets a custom logger for the plugin manager.
func WithLogger(logger slog.Handler) ManagerOption {
	return func(m *Manager) {
		m.logger = slog.New(logger)
	}
}

// WithHostFunctions adds host functions to the plugin manager.
func WithHostFunctions(functions ...extism.HostFunction) ManagerOption {
	return func(m *Manager) {
		m.hostFunctions = append(m.hostFunctions, functions...)
	}
}
