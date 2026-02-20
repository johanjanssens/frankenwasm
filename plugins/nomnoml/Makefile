export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building nomnoml plugin ..."
	set -e
	npm install
	node esbuild.js
	extism-js dist/index.js -i src/index.d.ts -o ../nomnoml.wasm
	echo "Built nomnoml.wasm"
