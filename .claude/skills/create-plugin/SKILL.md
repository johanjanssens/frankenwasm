---
name: create-plugin
description: Create a new FrankenWASM plugin using the Extism PDK. Scaffolds source code, Makefile, and demo page in Go, Rust, or JavaScript.
argument-hint: <plugin-name> <language: go|rust|js>
---

# Create a FrankenWASM Plugin

Create a new Extism WASM plugin for FrankenWASM. The plugin name and language are provided as arguments.

## References

- Extism overview: https://extism.org/docs/overview
- Go PDK: https://extism.org/docs/write-a-plugin/go-pdk
- Rust PDK: https://extism.org/docs/write-a-plugin/rust-pdk
- JS PDK: https://extism.org/docs/write-a-plugin/js-pdk

## Steps

1. **Create plugin directory**: `plugins/<name>/`
2. **Write plugin source** using the appropriate Extism PDK (see templates below)
3. **Create a Makefile** with a `build` target that outputs `../NAME.wasm`
4. **Add build step** to root `Makefile` under the `plugins` target
5. **Create demo page** at `examples/<name>/index.php`
6. **Add card** to `examples/index.php` and update navigation links

## Go Plugin Template

Directory: `plugins/<name>/`

### go.mod
```
module plugins/<name>

go 1.26.0

require github.com/extism/go-pdk v1.1.0
```

### main.go
```go
package main

import (
    "encoding/json"
    "github.com/extism/go-pdk"
)

// Params defines the input structure
type Params struct {
    // Define your input fields here
    Input string `json:"input"`
}

//go:wasmexport functionname
func functionname() int32 {
    params := &Params{}
    if err := json.Unmarshal(pdk.Input(), params); err != nil {
        pdk.SetError(err)
        return 1
    }

    // Process input...
    result := params.Input

    pdk.OutputString(result)
    return 0
}
```

### Makefile
```makefile
export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building <name> plugin ..."
	GOOS=wasip1 GOARCH=wasm go build -buildmode=c-shared -tags std -o ../<name>.wasm
	echo "Built <name>.wasm"
```

**Key notes for Go plugins:**
- Use standard Go compiler (not TinyGo) — TinyGo doesn't support Go 1.26
- Export functions with `//go:wasmexport name` (not `//export`)
- Input: `pdk.Input()` returns `[]byte`, `pdk.InputString()` returns `string`
- Output: `pdk.Output([]byte)` or `pdk.OutputString(string)`
- Config: `pdk.GetConfig("key")` returns `(string, bool)`
- Errors: `pdk.SetError(err)` then `return 1`
- If the function accepts a plain string from PHP, JSON-unwrap it first (the PHP extension JSON-encodes all arguments):
  ```go
  input := pdk.Input()
  var str string
  if err := json.Unmarshal(input, &str); err == nil {
      input = []byte(str)
  }
  ```

## Rust Plugin Template

Directory: `plugins/<name>/`

### Cargo.toml
```toml
[package]
name = "<name>"
version = "0.1.0"
edition = "2021"

[dependencies]
extism-pdk = "1.3.0"
serde_json = "1"

[lib]
crate-type = ["cdylib"]
```

### src/lib.rs
```rust
use extism_pdk::*;

#[plugin_fn]
pub fn functionname(input: String) -> FnResult<String> {
    // Parse JSON input if structured
    // let params: serde_json::Value = serde_json::from_str(&input)?;

    // Process input...
    let result = input;

    Ok(result)
}
```

### Makefile
```makefile
export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building <name> plugin ..."
	cargo build --target wasm32-wasip1 --release
	cp target/wasm32-wasip1/release/<name_underscored>.wasm ../<name>.wasm
	echo "Built <name>.wasm"
```

**Key notes for Rust plugins:**
- Target: `wasm32-wasip1`
- The `#[plugin_fn]` macro handles input/output automatically
- Input comes as `String` (already decoded from bytes)
- Return `FnResult<String>` for string output
- For structured input, use `serde_json::from_str()`
- The compiled filename uses underscores (Cargo convention), copy with hyphens to match plugin name

## JavaScript Plugin Template

Directory: `plugins/<name>/`

### src/index.js
```javascript
// IMPORTANT: Lazy-load heavy npm packages inside functions, NOT at top level.
// Top-level require() runs during Wizer pre-initialization when host functions
// aren't available yet. If the library calls host functions during require(),
// it will trap with "attempted to call unknown imported function".

function functionname() {
    const input = JSON.parse(Host.inputString());

    // Lazy-load if needed:
    // const lib = require('some-heavy-lib');

    // Process input...
    const result = input;

    Host.outputString(JSON.stringify(result));
}

module.exports = { functionname };
```

### src/index.d.ts
```typescript
declare module "main" {
    export function functionname(): I32;
}
```

### package.json
```json
{
    "name": "<name>-plugin",
    "private": true,
    "dependencies": {
        "your-lib": "^1.0.0"
    },
    "devDependencies": {
        "esbuild": "^0.20.0"
    }
}
```

### esbuild.js
```javascript
const esbuild = require('esbuild');

esbuild.build({
    entryPoints: ['src/index.js'],
    bundle: true,
    outfile: 'dist/index.js',
    format: 'cjs',
    target: 'es2020',
    mainFields: ['main', 'module'],
    minify: true,
    external: ['fs', 'path'],
}).catch(() => process.exit(1));
```

### Makefile
```makefile
export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building <name> plugin ..."
	set -e
	npm install
	node esbuild.js
	extism-js dist/index.js -i src/index.d.ts -o ../<name>.wasm
	echo "Built <name>.wasm"
```

**Key notes for JS plugins:**
- Uses QuickJS compiled to WASM (via `extism-js` tool) — no Node.js APIs at runtime
- Input: `Host.inputString()`, Output: `Host.outputString()`
- esbuild bundles npm dependencies into a single CJS file
- `extism-js` compiles the bundle + type declarations into a `.wasm` file using Wizer
- Always use `mainFields: ['main', 'module']` in esbuild config
- Add Node builtins to `external` if dependencies reference them: `['fs', 'path', 'url']`
- **Critical**: Lazy-load libraries that call host functions or access heavy APIs during `require()`. Move the `require()` inside the exported function.
- Declare all exported functions in `src/index.d.ts`

## Root Makefile Integration

Add to the `plugins` target in the root `Makefile`:
```makefile
cd $(ROOT)/plugins/<name> && $(MAKE) build
```

## Demo Page

Create `examples/<name>/index.php` following the patterns in existing demo pages. All demos share:
- `FrankenPHP\Wasm` namespace usage
- Light/dark theme toggle with localStorage persistence
- Navigation links to adjacent demos
- `Wasm::metadata()` for file size display
- `hrtime(true)` timing around `$plugin->call()`
