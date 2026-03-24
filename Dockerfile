# ──────────────────────────────────────────────
# Global ARGs (shared across stages)
# ──────────────────────────────────────────────
ARG PHP_VERSION=8.3
ARG PHP_EXTENSIONS="bcmath,ctype,curl,dom,filter,iconv,mbstring,opcache,openssl,pdo,pdo_sqlite,phar,posix,session,simplexml,sockets,sqlite3,tokenizer,xml,zlib"
ARG PHP_EXTENSION_LIBS="nghttp2"

# ──────────────────────────────────────────────
# Stage 1: Build PHP (ZTS + embed) via static-php-cli
# ──────────────────────────────────────────────
FROM ubuntu:24.04 AS php-builder

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl ca-certificates \
    make bison re2c flex git autoconf automake autopoint unzip \
    gcc g++ bzip2 cmake patch xz-utils libtool pkg-config && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /php

ARG PHP_VERSION
ARG PHP_EXTENSIONS
ARG PHP_EXTENSION_LIBS
RUN ARCH=$(uname -m) && \
    case "$ARCH" in \
        x86_64)  SPC="spc-linux-x86_64" ;; \
        aarch64) SPC="spc-linux-aarch64" ;; \
    esac && \
    curl -fsSL -o spc "https://dl.static-php.dev/static-php-cli/spc-bin/nightly/${SPC}" && \
    chmod +x spc

RUN --mount=type=secret,id=github_token,env=GITHUB_TOKEN \
    ./spc doctor --auto-fix && \
    ./spc download \
        --with-php=${PHP_VERSION} \
        --for-extensions="${PHP_EXTENSIONS}" \
        --for-libs="${PHP_EXTENSION_LIBS}" \
        --ignore-cache-sources=php-src \
        --prefer-pre-built \
        --retry 5 && \
    ./spc build --enable-zts --build-embed --disable-opcache-jit \
        "${PHP_EXTENSIONS}" \
        --with-libs="${PHP_EXTENSION_LIBS}"

# ──────────────────────────────────────────────
# Stage 2a: Build Go WASM plugins
# ──────────────────────────────────────────────
FROM golang:1.26 AS go-plugins

WORKDIR /plugins

COPY plugins/markdown-go/ markdown-go/
COPY plugins/chroma/       chroma/
COPY plugins/minify/       minify/
COPY plugins/qrcode/       qrcode/
COPY plugins/sanitize/     sanitize/

RUN --mount=type=cache,target=/go/pkg/mod \
    cd markdown-go && GOOS=wasip1 GOARCH=wasm go build -buildmode=c-shared -tags std -o ../markdown.wasm ./...
RUN --mount=type=cache,target=/go/pkg/mod \
    cd chroma      && GOOS=wasip1 GOARCH=wasm go build -buildmode=c-shared -tags std -o ../chroma.wasm ./...
RUN --mount=type=cache,target=/go/pkg/mod \
    cd minify      && GOOS=wasip1 GOARCH=wasm go build -buildmode=c-shared -tags std -o ../minify.wasm ./...
RUN --mount=type=cache,target=/go/pkg/mod \
    cd qrcode      && GOOS=wasip1 GOARCH=wasm go build -buildmode=c-shared -tags std -o ../qrcode.wasm ./...
RUN --mount=type=cache,target=/go/pkg/mod \
    cd sanitize    && GOOS=wasip1 GOARCH=wasm go build -buildmode=c-shared -tags std -o ../sanitize.wasm ./...

# ──────────────────────────────────────────────
# Stage 2b: Build Rust WASM plugins
# ──────────────────────────────────────────────
FROM rust:1.94 AS rust-plugins

RUN rustup target add wasm32-wasip1

WORKDIR /plugins

COPY plugins/markdown-rs/   markdown-rs/
COPY plugins/jsonpath-rs/   jsonpath-rs/
COPY plugins/toml-rs/       toml-rs/
COPY plugins/sanitize-rs/   sanitize-rs/
COPY plugins/scss-rs/       scss-rs/
COPY plugins/blurhash-rs/   blurhash-rs/
COPY plugins/ascii-rs/      ascii-rs/
COPY plugins/langdetect-rs/ langdetect-rs/

RUN --mount=type=cache,target=/usr/local/cargo/registry \
    cd markdown-rs   && cargo build --target wasm32-wasip1 --release && cp target/wasm32-wasip1/release/markdown_rs.wasm ../markdown-rs.wasm
RUN --mount=type=cache,target=/usr/local/cargo/registry \
    cd jsonpath-rs   && cargo build --target wasm32-wasip1 --release && cp target/wasm32-wasip1/release/jsonpath_rs.wasm ../jsonpath-rs.wasm
RUN --mount=type=cache,target=/usr/local/cargo/registry \
    cd toml-rs       && cargo build --target wasm32-wasip1 --release && cp target/wasm32-wasip1/release/toml_rs.wasm ../toml-rs.wasm
RUN --mount=type=cache,target=/usr/local/cargo/registry \
    cd sanitize-rs   && cargo build --target wasm32-wasip1 --release && cp target/wasm32-wasip1/release/sanitize_rs.wasm ../sanitize-rs.wasm
RUN --mount=type=cache,target=/usr/local/cargo/registry \
    cd scss-rs       && cargo build --target wasm32-wasip1 --release && cp target/wasm32-wasip1/release/scss_rs.wasm ../scss-rs.wasm
RUN --mount=type=cache,target=/usr/local/cargo/registry \
    cd blurhash-rs   && cargo build --target wasm32-wasip1 --release && cp target/wasm32-wasip1/release/blurhash_rs.wasm ../blurhash-rs.wasm
RUN --mount=type=cache,target=/usr/local/cargo/registry \
    cd ascii-rs      && cargo build --target wasm32-wasip1 --release && cp target/wasm32-wasip1/release/ascii_rs.wasm ../ascii-rs.wasm
RUN --mount=type=cache,target=/usr/local/cargo/registry \
    cd langdetect-rs && cargo build --target wasm32-wasip1 --release && cp target/wasm32-wasip1/release/langdetect_rs.wasm ../langdetect-rs.wasm

# ──────────────────────────────────────────────
# Stage 2c: Build JS WASM plugins
# ──────────────────────────────────────────────
FROM node:20-trixie AS js-plugins

# Install binaryen (wasm-merge, wasm-opt) and extism-js
RUN apt-get update && apt-get install -y --no-install-recommends binaryen && \
    rm -rf /var/lib/apt/lists/* && \
    ARCH=$(uname -m) && \
    curl -fsSL "https://github.com/extism/js-pdk/releases/download/v1.6.0/extism-js-${ARCH}-linux-v1.6.0.gz" | gunzip > /usr/local/bin/extism-js && \
    chmod +x /usr/local/bin/extism-js

WORKDIR /plugins

COPY plugins/markdown-js/   markdown-js/
COPY plugins/enhance-ssr/   enhance-ssr/
COPY plugins/jsx-include/   jsx-include/
COPY plugins/html-rewriter/ html-rewriter/
COPY plugins/katex/         katex/
COPY plugins/nomnoml/       nomnoml/
COPY plugins/sanitize-js/   sanitize-js/

RUN cd markdown-js   && npm install && node esbuild.js && extism-js dist/index.js -i src/index.d.ts -o ../markdown-js.wasm
RUN cd enhance-ssr   && npm install && node esbuild.js && extism-js dist/index.js -i src/index.d.ts -o ../enhance-ssr.wasm
RUN cd jsx-include   && npm install && node esbuild.js && extism-js dist/index.js -i src/index.d.ts -o ../jsx-include.wasm
RUN cd html-rewriter && npm install && node esbuild.js && extism-js dist/index.js -i src/index.d.ts -o ../html-rewriter.wasm
RUN cd katex         && npm install && node esbuild.js && extism-js dist/index.js -i src/index.d.ts -o ../katex.wasm
RUN cd nomnoml       && npm install && node esbuild.js && extism-js dist/index.js -i src/index.d.ts -o ../nomnoml.wasm
RUN cd sanitize-js   && npm install && node esbuild.js && extism-js dist/index.js -i src/index.d.ts -o ../sanitize-js.wasm

# ──────────────────────────────────────────────
# Stage 3: Build the Go host binary
# ──────────────────────────────────────────────
FROM golang:1.26-alpine AS host-builder

RUN apk add --no-cache git build-base

# Copy PHP build (headers, libs, spc binary for flag resolution)
COPY --from=php-builder /php /php

ARG PHP_EXTENSIONS
ARG PHP_EXTENSION_LIBS

# Clone the FrankenPHP fork
RUN git clone --depth 1 https://github.com/johanjanssens/frankenphp /build/frankenphp

WORKDIR /build/frankenwasm
COPY . .

# Point go.mod replace at the cloned fork
RUN go mod edit -replace "$(grep '^replace' go.mod | awk '{print $2 "@" $3}')=/build/frankenphp"

# Resolve CGO flags from the PHP build and compile
RUN --mount=type=cache,target=/go/pkg/mod \
    CGO_CFLAGS="$(cd /php && ./spc spc-config ${PHP_EXTENSIONS} --with-libs=${PHP_EXTENSION_LIBS} --includes)" && \
    CGO_CFLAGS="-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64 ${CGO_CFLAGS}" && \
    CGO_LDFLAGS="$(cd /php && ./spc spc-config ${PHP_EXTENSIONS} --with-libs=${PHP_EXTENSION_LIBS} --libs)" && \
    CGO_LDFLAGS="-Wl,-O1 -pie ${CGO_LDFLAGS}" && \
    export CGO_ENABLED=1 CGO_CFLAGS CGO_CPPFLAGS="$CGO_CFLAGS" CGO_LDFLAGS && \
    go build -tags nowatcher -o /frankenwasm .

# ──────────────────────────────────────────────
# Stage 4: Runtime
# ──────────────────────────────────────────────
FROM alpine:3.21

RUN apk add --no-cache ca-certificates

WORKDIR /app

COPY --from=host-builder /frankenwasm ./frankenwasm
COPY --from=go-plugins   /plugins/*.wasm ./plugins/
COPY --from=rust-plugins /plugins/*.wasm ./plugins/
COPY --from=js-plugins   /plugins/*.wasm ./plugins/
COPY examples/ ./examples/

ENV FRANKENWASM_PLUGIN_DIR=plugins
ENV FRANKENWASM_DOC_ROOT=examples

EXPOSE 8080

CMD ["./frankenwasm"]
