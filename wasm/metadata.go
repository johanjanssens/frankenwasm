package wasm

// Metadata holds information about a loaded plugin.
type Metadata struct {
	Name     string `json:"name"`
	FileSize int64  `json:"file_size"`
}
