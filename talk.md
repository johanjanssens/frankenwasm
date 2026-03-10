# Building PHP Extensions with WebAssembly

> Base narrative for FrankenPHP conference talks

## Abstract

What if PHP extensions could be written not just in C, but in almost any programming language — and
run faster than pure PHP code?

With WebAssembly and FrankenPHP, this is possible. Wasm lets you create PHP extensions using Rust,
Go, or JS that run in a secure sandbox and often outperform equivalent PHP implementations. A Rust
Markdown parser that converts in under 1 ms — packaged in a 644 KB `.wasm` file. HTML sanitization
in 0.8 ms. Sub-millisecond JSX server-side rendering and JSONPath queries. All as portable `.wasm`
files that work on any platform without recompiling.

This talk shows how you can build multi-language Wasm plugins for PHP with minimal code, why they're
faster, and how to run them with FrankenPHP.

## The Problem

PHP extensions are powerful — but writing them means C, the Zend API, manual memory management, and
a steep learning curve. Most PHP developers never write one. The barrier is too high, the risk of
segfaults too real, and the deployment story (compile per platform, match PHP versions) too painful.

Meanwhile, the ecosystem has evolved. Rust has world-class markdown parsers, Go has syntax
highlighters, JavaScript has JSX renderers. All of these exist as mature, battle-tested libraries.
But none of them can be called from PHP without writing a C wrapper, an FFI binding, or shelling
out to an external process.

What if there was a way to use these libraries directly from PHP — safely, portably, and without C?

## The Key Insight

WebAssembly provides a universal compile target. Code written in Rust, Go, or JavaScript can be
compiled to `.wasm` files that run in a sandboxed virtual machine. The Extism framework provides
the plugin SDK — a simple input/output contract that works across all three languages.

FrankenPHP embeds PHP in Go, giving us a natural place to host the Wasm runtime. The Go process
loads `.wasm` files at startup, and a thin C extension exposes them to PHP as a native class.

```
PHP: $md->call('convert', '# Hello')
    -> C extension: PHP_METHOD(Wasm, call)
    -> Go: wasm.Call("convert", input)
    -> Extism SDK -> Wasm VM
    -> Plugin executes (Rust/Go/JS)
    -> Result flows back to PHP
```

The PHP developer sees a simple method call. The plugin developer writes in their preferred language.
The Wasm sandbox guarantees isolation.

## What This Means

- **Any language** — write plugins in Rust, Go, or JavaScript. Use the best library for the job,
  regardless of what language it's written in.
- **Safe by default** — Wasm plugins run in a sandbox. They can't access the filesystem, network,
  or memory of the host process unless explicitly allowed.
- **Portable** — a `.wasm` file runs on any OS and architecture. No per-platform compilation,
  no PHP version coupling.
- **Simple API** — the PHP side is one class: `new Wasm('plugin')` and `$plugin->call('function', $args)`.
  Arguments are auto JSON-encoded, results auto JSON-decoded.
- **Small footprint** — Rust plugins compile to 256 KB–2.7 MB. Even Go and JS plugins stay under 9 MB.

## Writing a Plugin

The Extism PDK makes plugin development minimal. Here's a complete Rust plugin that converts
Markdown to HTML:

```rust
use extism_pdk::*;

#[plugin_fn]
pub fn convert(text: String) -> FnResult<String> {
    let mut options = markdown::Options::gfm();
    options.compile.allow_dangerous_html = true;
    options.compile.allow_dangerous_protocol = true;

    let result = markdown::to_html_with_options(&text, &options)
        .unwrap_or_else(|_| text.clone());

    Ok(result)
}
```

Build it: `cargo build --target wasm32-wasip1 --release`. Drop the `.wasm` file in the plugins
directory. Done.

The same plugin in Go:

```go
//go:wasmexport convert
func convert() int32 {
    input := pdk.Input()

    extensions := parser.CommonExtensions | parser.AutoHeadingIDs
    p := parser.NewWithExtensions(extensions)

    opts := html.RendererOptions{Flags: html.CommonFlags | html.HrefTargetBlank}
    renderer := html.NewRenderer(opts)

    output := markdown.ToHTML(input, p, renderer)
    pdk.Output(output)
    return 0
}
```

And in JavaScript:

```javascript
import markdownit from 'markdown-it';

export function convert() {
    const input = Host.inputString();
    const md = markdownit();
    Host.outputString(md.render(input));
}
```

Three languages, same function signature, same `.wasm` output format. PHP doesn't know or care
which language was used.

## Calling Plugins from PHP

```php
use FrankenPHP\Wasm;

$md = new Wasm('markdown');
$html = $md->call('convert', '# Hello World');

$chroma = new Wasm('chroma');
$highlighted = $chroma->call('transform', [
    'code' => '<?php echo "hi";',
    'lang' => 'php',
]);
```

The `call()` method JSON-encodes the input, calls the Wasm function, and JSON-decodes the result.
Structured data flows naturally between PHP and the plugin.

## The Language Comparison

The same task implemented in all three languages reveals clear trade-offs:

### Plugin Size

| Language | Example Plugin | Size |
|----------|---------------|------|
| Rust | ASCII art (5 embedded fonts) | 256 KB |
| Rust | Markdown (markdown-rs) | 644 KB |
| Rust | HTML sanitization (ammonia) | 1.2 MB |
| Go | QR code generation | 3.6 MB |
| Go | HTML sanitization (bluemonday) | 4.3 MB |
| Go | Syntax highlighting (chroma) | 8.3 MB |
| JS | Markdown (markdown-it) | 3.4 MB |
| JS | HTML sanitization (sanitize-html) | 3.9 MB |
| JS | React JSX rendering | 7.0 MB |

Rust consistently produces the smallest binaries — no garbage collector, no runtime. Go includes
its GC and runtime, landing in the 2–8 MB range. JavaScript bundles npm dependencies plus the
QuickJS runtime.

### When to Use Which

- **Rust** — performance-critical plugins, smallest binaries, best for computation-heavy tasks
- **Go** — when the best library is in Go, or when you're already a Go developer
- **JavaScript** — when the npm ecosystem has what you need (JSX, KaTeX, cheerio)

## Demo Walkthrough

### Markdown — three implementations compared

The same Markdown input rendered by Go (gomarkdown, ~2 ms), Rust (markdown-rs, ~1 ms), and
JS (markdown-it, ~26 ms) side by side. Same HTML output, very different performance profiles.
Rust is consistently the fastest, Go close behind, JS significantly slower due to the QuickJS
interpreter overhead.

### Syntax Highlighting — Chroma via Wasm

PHP sends source code and a language identifier. The Go Chroma library tokenizes and highlights it,
returning styled HTML. Configurable themes, line numbers, and language detection.

### HTML Sanitization — Go vs Rust vs JS

The same dirty HTML sanitized by three different libraries in three different languages: Go
(bluemonday, ~22 ms), Rust (ammonia, ~0.8 ms), JS (sanitize-html, ~2 ms). Rust wins by a wide
margin. Demonstrates that you can pick the best tool regardless of language.

### SCSS Compiler — Rust's grass crate

Compile SCSS to CSS from PHP. Variables, nesting, mixins, functions — the full Sass spec
implemented in Rust, running in a Wasm sandbox, called from PHP with a single method.

### Language Detection — whatlang in Rust

Pass text in any language — the Rust whatlang crate identifies the language, script, and confidence
level. A capability that would be extremely difficult to add to PHP natively.

## Architecture

The host application is ~180 lines of Go:

1. **Discover plugins** — glob `plugins/*.wasm` at startup
2. **Compile and cache** — Extism compiles each `.wasm` file, wazero caches the compilation
3. **Instantiate per request** — each HTTP request gets its own plugin instances (thread-safe)
4. **Expose to PHP** — a C extension registers the `FrankenPHP\Wasm` class, bridging PHP → Go → Wasm

The plugin registry is stored in the request context. Multiple PHP requests can execute Wasm
plugins concurrently without interference.

## Key Takeaway

PHP extensions don't have to be written in C. WebAssembly provides a safe, portable, multi-language
plugin system that any PHP developer can use. Write your plugin in Rust for performance, Go for
ecosystem, or JavaScript for convenience — compile to `.wasm`, drop it in, and call it from PHP.
FrankenPHP makes the hosting seamless.

---

This work is licensed under [CC BY 4.0](https://creativecommons.org/licenses/by/4.0/).
You are free to share and adapt this material with appropriate attribution.
