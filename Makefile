export MAKEFLAGS='--silent --environment-override'

ROOT := $(abspath $(dir $(lastword $(MAKEFILE_LIST))))

.ONESHELL:

.PHONY: build
build:
	if [ ! -f $(ROOT)/env.yaml ]; then
		echo "Error: env.yaml not found."
		echo "Run 'make php' to build PHP, then 'make env' to generate env.yaml."
		echo "See README.md for details."
		exit 1
	fi

	cd $(ROOT)
	CGO_ENABLED=1 go build -tags nowatcher -o dist/frankenwasm .
	echo "Built dist/frankenwasm"

.PHONY: run
run: build
	cd $(ROOT) && FRANKENWASM_PLUGIN_DIR=plugins FRANKENWASM_DOC_ROOT=examples dist/frankenwasm

.PHONY: clean
clean:
	rm -rf dist/frankenwasm

# PHP build targets (delegated to build/php/Makefile)
.PHONY: php
php:
	$(MAKE) -f $(ROOT)/build/php/Makefile download build

.PHONY: env
env:
	$(MAKE) -f $(ROOT)/build/php/Makefile env

.PHONY: php-clean
php-clean:
	$(MAKE) -f $(ROOT)/build/php/Makefile clean=1 clean

.PHONY: plugins
plugins:
	mkdir -p $(ROOT)/plugins
	cd $(ROOT)/plugins/markdown-go && GOOS=wasip1 GOARCH=wasm go build -buildmode=c-shared -tags std -o ../markdown.wasm ./... && echo "Built markdown.wasm"
	cd $(ROOT)/plugins/markdown-rs && $(MAKE) build
	cd $(ROOT)/plugins/jsonpath-rs && $(MAKE) build
	cd $(ROOT)/plugins/chroma && $(MAKE) build
	cd $(ROOT)/plugins/minify && $(MAKE) build
	cd $(ROOT)/plugins/markdown-js && $(MAKE) build
	cd $(ROOT)/plugins/enhance-ssr && $(MAKE) build
	cd $(ROOT)/plugins/jsx-include && $(MAKE) build
	cd $(ROOT)/plugins/html-rewriter && $(MAKE) build
	cd $(ROOT)/plugins/qrcode && $(MAKE) build
	cd $(ROOT)/plugins/toml-rs && $(MAKE) build
	cd $(ROOT)/plugins/katex && $(MAKE) build
	cd $(ROOT)/plugins/nomnoml && $(MAKE) build
	cd $(ROOT)/plugins/sanitize && $(MAKE) build
	cd $(ROOT)/plugins/sanitize-rs && $(MAKE) build
	cd $(ROOT)/plugins/sanitize-js && $(MAKE) build
	cd $(ROOT)/plugins/scss-rs && $(MAKE) build
	cd $(ROOT)/plugins/blurhash-rs && $(MAKE) build
	cd $(ROOT)/plugins/ascii-rs && $(MAKE) build
	cd $(ROOT)/plugins/langdetect-rs && $(MAKE) build

.PHONY: tidy
tidy:
	cd $(ROOT) && go mod tidy
