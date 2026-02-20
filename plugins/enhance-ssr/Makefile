export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building enhance-ssr plugin ..."
	set -e
	npm install
	node esbuild.js
	extism-js dist/index.js -i src/index.d.ts -o ../enhance-ssr.wasm
	echo "Built enhance-ssr.wasm"
