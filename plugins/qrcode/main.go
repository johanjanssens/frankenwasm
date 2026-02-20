package main

import (
	"encoding/json"
	"fmt"
	"strings"

	"github.com/extism/go-pdk"
	qrcode "github.com/skip2/go-qrcode"
)

type Input struct {
	Text string `json:"text"`
	Size int    `json:"size"`
}

func main() {}

//go:wasmexport generate
func generate() int32 {
	var input Input
	if err := json.Unmarshal(pdk.Input(), &input); err != nil {
		pdk.SetError(err)
		return 1
	}

	if input.Text == "" {
		pdk.SetErrorString("text is required")
		return 1
	}

	size := input.Size
	if size <= 0 {
		size = 256
	}

	qr, err := qrcode.New(input.Text, qrcode.Medium)
	if err != nil {
		pdk.SetError(err)
		return 1
	}
	qr.DisableBorder = true

	bitmap := qr.Bitmap()
	rows := len(bitmap)
	if rows == 0 {
		pdk.SetErrorString("empty QR code")
		return 1
	}
	cols := len(bitmap[0])

	cellSize := float64(size) / float64(cols)

	var svg strings.Builder
	svg.WriteString(fmt.Sprintf(`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" width="%d" height="%d">`, size, size, size, size))
	svg.WriteString(fmt.Sprintf(`<rect width="%d" height="%d" fill="white"/>`, size, size))

	for y := 0; y < rows; y++ {
		for x := 0; x < cols; x++ {
			if bitmap[y][x] {
				svg.WriteString(fmt.Sprintf(`<rect x="%.2f" y="%.2f" width="%.2f" height="%.2f" fill="black"/>`,
					float64(x)*cellSize, float64(y)*cellSize, cellSize+0.5, cellSize+0.5))
			}
		}
	}

	svg.WriteString(`</svg>`)

	pdk.OutputString(svg.String())
	return 0
}
