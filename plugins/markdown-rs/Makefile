export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building markdown-rs plugin ..."
	cargo build --target wasm32-wasip1 --release
	cp target/wasm32-wasip1/release/markdown_rs.wasm ../markdown-rs.wasm
	echo "Built markdown-rs.wasm"
