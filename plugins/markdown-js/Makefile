export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building markdown-js plugin ..."
	set -e
	npm install
	node esbuild.js
	extism-js dist/index.js -i src/index.d.ts -o ../markdown-js.wasm
	echo "Built markdown-js.wasm"
