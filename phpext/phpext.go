package phpext

// #include <stdlib.h>
// #include <stdint.h>
// #cgo CFLAGS: -I../../frankenphp
// #include "frankenphp.h"
// #include "phpext.h"
//
import "C"
import (
	"encoding/json"
	"unsafe"

	"github.com/johanjanssens/frankenwasm/wasm"

	"github.com/dunglas/frankenphp"
)

func init() {
	frankenphp.RegisterExtension(unsafe.Pointer(&C.frankenwasm_module_entry))
}

//export go_wasm_list
func go_wasm_list(threadIndex C.uintptr_t) (*C.char, C.bool) {
	thread, ok := frankenphp.Thread(int(threadIndex))
	if !ok || thread.IsRequestDone() {
		return C.CString("Thread not available"), C.bool(false)
	}

	ctx := thread.Request.Context()
	plugins := wasm.FromContext(ctx)
	if plugins == nil {
		return C.CString("No plugin registry in context"), C.bool(false)
	}

	pluginNames := plugins.Names()
	jsonData, err := json.Marshal(pluginNames)
	if err != nil {
		return nil, C.bool(false)
	}

	return C.CString(string(jsonData)), C.bool(true)
}

//export go_wasm_metadata
func go_wasm_metadata(threadIndex C.uintptr_t) (*C.char, C.bool) {
	thread, ok := frankenphp.Thread(int(threadIndex))
	if !ok || thread.IsRequestDone() {
		return C.CString("Thread not available"), C.bool(false)
	}

	ctx := thread.Request.Context()
	plugins := wasm.FromContext(ctx)
	if plugins == nil {
		return C.CString("No plugin registry in context"), C.bool(false)
	}

	metadata := plugins.Metadata()
	jsonData, err := json.Marshal(metadata)
	if err != nil {
		return nil, C.bool(false)
	}

	return C.CString(string(jsonData)), C.bool(true)
}

//export go_wasm_exists
func go_wasm_exists(threadIndex C.uintptr_t, name *C.char) C.bool {
	thread, ok := frankenphp.Thread(int(threadIndex))
	if !ok || thread.IsRequestDone() {
		return C.bool(false)
	}

	ctx := thread.Request.Context()
	plugins := wasm.FromContext(ctx)
	if plugins == nil {
		return C.bool(false)
	}

	return C.bool(plugins.Exists(C.GoString(name)))
}

//export go_wasm_call
func go_wasm_call(threadIndex C.uintptr_t, name *C.char, function *C.char, args *C.char) (*C.char, C.bool) {
	thread, ok := frankenphp.Thread(int(threadIndex))
	if !ok || thread.IsRequestDone() {
		return C.CString("Thread not available"), C.bool(false)
	}

	ctx := thread.Request.Context()
	plugins := wasm.FromContext(ctx)
	if plugins == nil {
		return C.CString("No plugin registry in context"), C.bool(false)
	}

	result, err := plugins.Call(ctx, C.GoString(name), C.GoString(function), C.GoString(args))

	if err != nil {
		return C.CString(err.Error()), C.bool(false)
	}

	if result == nil {
		return C.CString("failed to call plugin"), C.bool(false)
	}

	return C.CString(string(result)), C.bool(true)
}
