export MAKEFLAGS='--silent --environment-override'

.ONESHELL:

.PHONY: build
build:
	echo "Building sanitize-rs plugin ..."
	cargo build --target wasm32-wasip1 --release
	cp target/wasm32-wasip1/release/sanitize_rs.wasm ../sanitize-rs.wasm
	echo "Built sanitize-rs.wasm"
