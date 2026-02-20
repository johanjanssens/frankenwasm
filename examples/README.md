# FrankenWASM Examples

Interactive demo pages for all FrankenWASM plugins. Each demo exercises one or more plugins with sample inputs and shows the rendered output alongside the PHP source code.

## Running

```bash
cd /path/to/frankenwasm
make plugins   # build all .wasm files
make run       # build host + start server on :8080
```

Open `http://localhost:8080` to see the index page with all demos.

## Demos

| Demo | Path | Plugins Used | Description |
|---|---|---|---|
| **Index** | `/` | all | Overview with plugin cards and file sizes |
| **Markdown** | `/markdown/` | markdown, markdown-rs, markdown-js | Side-by-side Go/Rust/JS comparison |
| **Syntax Highlighting** | `/highlight/` | chroma | Code highlighting with configurable themes |
| **JSONPath** | `/jsonpath/` | jsonpath-rs | Query JSON data with JSONPath expressions |
| **Minify** | `/minify/` | minify | CSS/JS/HTML/SVG minification with size metrics |
| **Enhance SSR** | `/enhance-ssr/` | enhance-ssr | Custom element server-side rendering |
| **JSX / React SSR** | `/jsx-include/` | jsx-include | Inline JSX compiled and rendered server-side |
| **HTML Rewriter** | `/html-rewriter/` | html-rewriter | DOM transformations via JavaScript functions |
| **QR Code** | `/qrcode/` | qrcode | SVG QR codes from text, URLs, WiFi, vCards |
| **TOML** | `/toml/` | toml-rs | Bidirectional TOML ↔ JSON conversion |
| **KaTeX** | `/katex/` | katex | LaTeX math expressions rendered to HTML |
| **Diagrams** | `/diagrams/` | nomnoml | UML, flowchart, and architecture diagrams |
| **HTML Sanitize** | `/sanitize/` | sanitize, sanitize-rs, sanitize-js | Go/Rust/JS sanitization comparison |

## Features

All demo pages include:
- **Light/dark theme** toggle with system preference detection and localStorage persistence
- **Navigation** between all demos via prev/next links
- **Plugin file sizes** shown via `Wasm::metadata()`
- **Execution timing** with `hrtime()` measurements
- **Source code** displayed alongside output

## PHP API Usage Pattern

Every demo follows the same pattern:

```php
use FrankenPHP\Wasm;

$plugin = new Wasm('plugin-name');
$result = $plugin->call('function-name', $input);
```

Where `$input` can be a string or an associative array (auto JSON-encoded).
