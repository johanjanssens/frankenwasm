export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building scss-rs plugin ..."
	cargo build --target wasm32-wasip1 --release
	cp target/wasm32-wasip1/release/scss_rs.wasm ../scss-rs.wasm
	echo "Built scss-rs.wasm"
