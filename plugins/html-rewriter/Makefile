export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building html-rewriter plugin ..."
	set -e
	npm install
	node esbuild.js
	extism-js dist/index.js -i src/index.d.ts -o ../html-rewriter.wasm
	echo "Built html-rewriter.wasm"
