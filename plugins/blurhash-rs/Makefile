export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building blurhash-rs plugin ..."
	cargo build --target wasm32-wasip1 --release
	cp target/wasm32-wasip1/release/blurhash_rs.wasm ../blurhash-rs.wasm
	echo "Built blurhash-rs.wasm"
