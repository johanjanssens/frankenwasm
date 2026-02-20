export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building ascii-rs plugin ..."
	cargo build --target wasm32-wasip1 --release
	cp target/wasm32-wasip1/release/ascii_rs.wasm ../ascii-rs.wasm
	echo "Built ascii-rs.wasm"
