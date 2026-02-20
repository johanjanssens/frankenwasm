export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building minify plugin ..."
	GOOS=wasip1 GOARCH=wasm go build -buildmode=c-shared -tags std -o ../minify.wasm ./...
	echo "Built minify.wasm"
