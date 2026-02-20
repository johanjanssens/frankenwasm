# FrankenWASM

WebAssembly plugin runtime for PHP using FrankenPHP + Extism.

## Architecture

- `main.go` — HTTP server. Discovers `.wasm` files in `plugins/`, loads them via the Extism Go SDK, starts FrankenPHP, and serves PHP pages.
- `wasm/` — Plugin management layer. `Manager` loads plugins, `Registry` provides per-request instances (cloned from a base registry for thread safety).
- `phpext/` — C + Go PHP extension that exposes `FrankenPHP\Wasm` class to PHP. Uses CGO to bridge Go exports ↔ C PHP methods.
- `plugins/` — Plugin source code (Go, Rust, JS subdirectories) and built `.wasm` files.
- `examples/` — PHP demo pages served as the document root.

## Build

```bash
make plugins   # Build all .wasm plugin files
make build     # Build the Go host binary (dist/frankenwasm)
make run       # Build + run (serves on :8080)
make env       # Auto-generate env.yaml for GoLand
```

The Go binary requires CGO with PHP headers and a static PHP build. If `env.yaml` exists, the Makefile skips flag resolution and uses the cached environment. Run `make env` to generate it from a local PHP build.

Build tag: `nowatcher` (required — file watcher is not used).

For GoLand, run `make env` to generate `env.yaml`, then configure GoLand to load it.

## Plugin Languages

### Go plugins
- Use standard Go compiler: `GOOS=wasip1 GOARCH=wasm go build -buildmode=c-shared -tags std`
- Import `github.com/extism/go-pdk`
- Export functions with `//go:wasmexport functionname`
- Input: `pdk.Input()` (bytes) or `pdk.InputString()` (string)
- Output: `pdk.Output()` or `pdk.OutputString()`
- Config: `pdk.GetConfig("key")`

### Rust plugins
- Target: `wasm32-wasip1`, crate-type `cdylib`
- Import `extism-pdk`
- Export functions with `#[plugin_fn]`
- Input/output via function signature: `pub fn name(input: String) -> FnResult<String>`

### JavaScript plugins
- Bundle with esbuild (`format: 'cjs'`, `target: 'es2020'`)
- Compile with `extism-js dist/index.js -i src/index.d.ts -o plugin.wasm`
- Input: `Host.inputString()`, Output: `Host.outputString()`
- Declare exports in `src/index.d.ts`
- **Important**: Lazy-load heavy npm packages inside functions (not at top level) to avoid Wizer initialization traps. If a library accesses host functions or heavy Node APIs during `require()`, it will fail during the Wizer pre-init phase.

## PHP API

```php
use FrankenPHP\Wasm;

// Static
Wasm::list();        // string[] — loaded plugin names
Wasm::metadata();    // array[] — [{name, file_size}, ...]

// Instance
$p = new Wasm('name');
$p->call('function', $args);  // $args auto JSON-encoded, result auto JSON-decoded
$p->getName();
```

## Adding a New Plugin

1. Create a directory under `plugins/` (e.g., `plugins/myplugin/`)
2. Write the plugin using the appropriate Extism PDK
3. Add a `Makefile` with a `build` target that outputs `../myplugin.wasm`
4. Add the build step to the root `Makefile` `plugins` target
5. Create a demo page under `examples/myplugin/index.php`
6. Add a card to `examples/index.php` and update navigation links

## Conventions

- Plugin `.wasm` files live in the `plugins/` directory root (not in subdirectories)
- Plugin source code lives in `plugins/<name>/`
- All demo pages use the same CSS patterns: light/dark theme toggle, nav bar, card layouts
- The PHP extension auto JSON-encodes arguments and attempts JSON-decode on results
- Keep esbuild configs consistent across JS plugins: `format: 'cjs'`, `target: 'es2020'`, `mainFields: ['main', 'module']`
