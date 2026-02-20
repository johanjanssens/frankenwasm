export MAKEFLAGS='--silent --environment-override'

ROOT := $(abspath $(dir $(lastword $(MAKEFILE_LIST))))
PHP_BUILD := $(abspath $(ROOT)/..)

.ONESHELL:

.PHONY: build
build:
	if [ ! -f $(ROOT)/env.yaml ]; then
		if [ "$$(uname -s)" = "Darwin" ]; then
			export MACOSX_DEPLOYMENT_TARGET="15.0"
		fi
		export CGO_CFLAGS="$$(make -C $(PHP_BUILD) cflags)"
		export CGO_CPPFLAGS="$$CGO_CFLAGS"
		export CGO_LDFLAGS="$$(make -C $(PHP_BUILD) ldflags)"
	fi

	cd $(ROOT)
	CGO_ENABLED=1 go build -tags nowatcher -o dist/frankenwasm .
	echo "Built dist/frankenwasm"

.PHONY: run
run: build
	cd $(ROOT) && FRANKENWASM_PLUGIN_DIR=plugins FRANKENWASM_DOC_ROOT=examples dist/frankenwasm

.PHONY: env
env:
	if [ "$$(uname -s)" = "Darwin" ]; then
		deployment_target='MACOSX_DEPLOYMENT_TARGET: "15.0"'
	else
		deployment_target=""
	fi

	cflags=$$(make -C $(PHP_BUILD) cflags)
	ldflags=$$(make -C $(PHP_BUILD) ldflags)

	cat > $(ROOT)/env.yaml <<-YAML
		HOME: "$$HOME"
		GOPATH: "$${GOPATH:-$$HOME/go}"
		GOFLAGS: "-tags=nowatcher"
		CGO_ENABLED: "1"
		$$deployment_target
		CGO_CFLAGS: "$$cflags"
		CGO_CPPFLAGS: "$$cflags"
		CGO_LDFLAGS: "$$ldflags"
	YAML

	echo "Generated env.yaml"

.PHONY: clean
clean:
	rm -rf dist/frankenwasm

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
