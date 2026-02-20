package main

import (
	"regexp"

	"github.com/extism/go-pdk"
	"github.com/tdewolff/minify/v2"
	"github.com/tdewolff/minify/v2/css"
	"github.com/tdewolff/minify/v2/html"
	"github.com/tdewolff/minify/v2/js"
	"github.com/tdewolff/minify/v2/svg"
)

var minifier *minify.M

func init() {
	minifier = minify.New()
	minifier.AddFunc("text/html", html.Minify)
	minifier.AddFunc("text/css", css.Minify)
	minifier.AddFuncRegexp(regexp.MustCompile("^(application|text)/(x-)?(java|ecma)script$"), js.Minify)
	minifier.AddFunc("image/svg+xml", svg.Minify)
}

func main() {}

//go:wasmexport css
func minifyCSS() int32 {
	output, err := minifier.Bytes("text/css", pdk.Input())
	if err != nil {
		pdk.SetError(err)
		return 1
	}
	pdk.Output(output)
	return 0
}

//go:wasmexport js
func minifyJS() int32 {
	output, err := minifier.Bytes("application/javascript", pdk.Input())
	if err != nil {
		pdk.SetError(err)
		return 1
	}
	pdk.Output(output)
	return 0
}

//go:wasmexport html
func minifyHTML() int32 {
	output, err := minifier.Bytes("text/html", pdk.Input())
	if err != nil {
		pdk.SetError(err)
		return 1
	}
	pdk.Output(output)
	return 0
}

//go:wasmexport svg
func minifySVG() int32 {
	output, err := minifier.Bytes("image/svg+xml", pdk.Input())
	if err != nil {
		pdk.SetError(err)
		return 1
	}
	pdk.Output(output)
	return 0
}
