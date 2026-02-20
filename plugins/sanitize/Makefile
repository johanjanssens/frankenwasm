export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building sanitize plugin ..."
	GOOS=wasip1 GOARCH=wasm go build -buildmode=c-shared -tags std -o ../sanitize.wasm
	echo "Built sanitize.wasm"
