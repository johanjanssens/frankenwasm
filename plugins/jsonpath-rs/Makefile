export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building jsonpath-rs plugin ..."
	cargo build --target wasm32-wasip1 --release
	cp target/wasm32-wasip1/release/jsonpath_rs.wasm ../jsonpath-rs.wasm
	echo "Built jsonpath-rs.wasm"
