package main

import (
	"encoding/json"

	"github.com/extism/go-pdk"
	"github.com/gomarkdown/markdown"
	"github.com/gomarkdown/markdown/html"
	"github.com/gomarkdown/markdown/parser"
)

//go:wasmexport convert
func convert() int32 {
	input := pdk.Input()

	// The input may be JSON-encoded (wrapped in quotes), try to unwrap it
	var str string
	if err := json.Unmarshal(input, &str); err == nil {
		input = []byte(str)
	}

	extensions := parser.CommonExtensions | parser.AutoHeadingIDs
	p := parser.NewWithExtensions(extensions)

	opts := html.RendererOptions{Flags: html.CommonFlags | html.HrefTargetBlank}
	renderer := html.NewRenderer(opts)

	output := markdown.ToHTML(input, p, renderer)
	pdk.Output(output)
	return 0
}
