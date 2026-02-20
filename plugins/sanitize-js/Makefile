export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building sanitize-js plugin ..."
	set -e
	npm install
	node esbuild.js
	extism-js dist/index.js -i src/index.d.ts -o ../sanitize-js.wasm
	echo "Built sanitize-js.wasm"
