export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building chroma plugin ..."
	GOOS=wasip1 GOARCH=wasm go build -buildmode=c-shared -tags std -o ../chroma.wasm
	echo "Built chroma.wasm"
