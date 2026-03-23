# FAQ

Common questions from the [ConFoo 2026 talk](https://confoo.ca/en/2026/session/building-php-extensions-with-webassembly) and social media.

---

### How does this compare to Extism's PHP SDK?

The PHP SDK uses FFI to call into libextism, which is Extism's own shared library. Under the hood, libextism is built on Wasmtime. The dependency chain is:

`PHP SDK → (FFI) → libextism → Wasmtime`

With FrankenWASM you get:

`PHP → C/Zend → CGO → Go/Wazero/Extism`

All in one process, compiled into a single binary. No shared libraries to install, no FFI extension to enable — you get a PHP runtime you can drop anywhere and extend with `.wasm` plugins. Plugins can just be dropped into place.

The main win is tighter integration — direct control over compilation caching, instance pooling, and per-request cloning without crossing an FFI boundary to a separate runtime.

### Why not just write PHP extensions in C?

You can — and for performance-critical, stable APIs it's still the right choice. But C extensions are tied to specific PHP versions, require the Zend API, need recompiling per platform, and run unsandboxed in the PHP process.

Wasm plugins are sandboxed, portable (compile once, run anywhere), and can be written in Rust, Go, or JavaScript. You trade some raw performance for developer experience and safety. A 12-line Rust HTML sanitizer runs in 0.8 ms — fast enough for most use cases.

### Can plugins access the filesystem or network?

Plugins run in a WASI sandbox with controlled capabilities. The current configuration allows outbound HTTP (for plugins that need to fetch resources), but filesystem access is restricted to what the host explicitly grants. Plugins cannot access PHP's memory, global state, or other plugins.