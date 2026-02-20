export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building qrcode plugin ..."
	GOOS=wasip1 GOARCH=wasm go build -buildmode=c-shared -tags std -o ../qrcode.wasm
	echo "Built qrcode.wasm"
