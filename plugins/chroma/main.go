package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"html"
	"strconv"

	chromahtml "github.com/alecthomas/chroma/v2/formatters/html"
	"github.com/alecthomas/chroma/v2/lexers"
	"github.com/alecthomas/chroma/v2/styles"
	"github.com/extism/go-pdk"
)

type HighlightParams struct {
	Code string `json:"code"`
	Lang string `json:"lang"`
}

type Config struct {
	Style               string
	LineNumbers         bool
	LineNumbersLinkable bool
	ClassPrefix         string
	IdPrefix            string
	WrapLongLines       bool
	TabWidth            int
	LineNumbersInTable  bool
}

var cfg Config

func init() {
	cfg = Config{
		Style:               "nord",
		LineNumbers:         false,
		LineNumbersLinkable: false,
		ClassPrefix:         "",
		IdPrefix:            "chroma",
		WrapLongLines:       false,
		TabWidth:            4,
		LineNumbersInTable:  false,
	}

	if style, ok := pdk.GetConfig("style"); ok {
		cfg.Style = style
	}
	if lineNumbers, ok := pdk.GetConfig("line_numbers"); ok {
		cfg.LineNumbers, _ = strconv.ParseBool(lineNumbers)
	}
	if lineNumbersLinkable, ok := pdk.GetConfig("line_numbers_linkable"); ok {
		cfg.LineNumbersLinkable, _ = strconv.ParseBool(lineNumbersLinkable)
	}
	if classPrefix, ok := pdk.GetConfig("class_prefix"); ok {
		cfg.ClassPrefix = classPrefix
	}
	if idPrefix, ok := pdk.GetConfig("id_prefix"); ok {
		cfg.IdPrefix = idPrefix
	}
	if wrapLongLines, ok := pdk.GetConfig("wrap_long_lines"); ok {
		cfg.WrapLongLines, _ = strconv.ParseBool(wrapLongLines)
	}
	if tabWidth, ok := pdk.GetConfig("tab_width"); ok {
		cfg.TabWidth, _ = strconv.Atoi(tabWidth)
	}
	if lineNumbersInTable, ok := pdk.GetConfig("line_numbers_in_table"); ok {
		cfg.LineNumbersInTable, _ = strconv.ParseBool(lineNumbersInTable)
	}
}

func main() {}

//go:wasmexport transform
func transform() int32 {
	params := &HighlightParams{}
	if err := json.Unmarshal(pdk.Input(), params); err != nil {
		pdk.SetError(err)
		return 1
	}

	if params.Lang == "" {
		pdk.SetError(fmt.Errorf("`lang` param is empty"))
		return 1
	}

	if params.Code == "" {
		pdk.SetError(fmt.Errorf("`code` param is empty"))
		return 1
	}

	result, err := highlightCode(params.Code, params.Lang)
	if err != nil {
		pdk.SetError(fmt.Errorf("failed to format code: %w", err))
		return 1
	}

	pdk.OutputString(result)
	return 0
}

func highlightCode(code string, lang string) (string, error) {
	code = html.UnescapeString(code)

	lexer := lexers.Fallback
	if lang != "" {
		lexer = lexers.Get(lang)
	} else {
		lexer = lexers.Analyse(code)
	}

	classPrefix := ""
	if cfg.ClassPrefix != "" {
		classPrefix = fmt.Sprintf("%s-", cfg.ClassPrefix)
	}

	style := styles.Get(cfg.Style)
	if style == nil {
		style = styles.Fallback
	}

	var buf bytes.Buffer
	iterator, err := lexer.Tokenise(nil, code)
	if err != nil {
		return "", err
	}

	idPrefix := fmt.Sprintf("%s-1-", cfg.IdPrefix)

	formatter := chromahtml.New(
		chromahtml.Standalone(false),
		chromahtml.WithClasses(false),
		chromahtml.WithLineNumbers(cfg.LineNumbers),
		chromahtml.WithLinkableLineNumbers(cfg.LineNumbersLinkable, idPrefix),
		chromahtml.ClassPrefix(classPrefix),
		chromahtml.WrapLongLines(cfg.WrapLongLines),
		chromahtml.LineNumbersInTable(cfg.LineNumbersInTable),
		chromahtml.TabWidth(cfg.TabWidth),
	)

	if err := formatter.Format(&buf, style, iterator); err != nil {
		return "", err
	}

	return buf.String(), nil
}
