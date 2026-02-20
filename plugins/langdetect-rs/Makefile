export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building langdetect-rs plugin ..."
	cargo build --target wasm32-wasip1 --release
	cp target/wasm32-wasip1/release/langdetect_rs.wasm ../langdetect-rs.wasm
	echo "Built langdetect-rs.wasm"
