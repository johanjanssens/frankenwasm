package main

import (
	"encoding/json"

	"github.com/extism/go-pdk"
	"github.com/microcosm-cc/bluemonday"
)

var policy *bluemonday.Policy

func init() {
	policy = bluemonday.UGCPolicy()
}

//go:wasmexport sanitize
func sanitize() int32 {
	input := pdk.Input()
	// JSON-unwrap string args from the PHP extension
	var str string
	if err := json.Unmarshal(input, &str); err == nil {
		input = []byte(str)
	}
	output := policy.Sanitize(string(input))
	pdk.OutputString(output)
	return 0
}

func main() {}
