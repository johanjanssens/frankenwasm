package wasm

import (
	"context"
)

type ctxKey struct{}

// WithContext sets the registry in the context.
func WithContext(ctx context.Context, plugins *Registry) context.Context {
	return context.WithValue(ctx, ctxKey{}, plugins)
}

// FromContext extracts the registry from the context.
func FromContext(ctx context.Context) *Registry {
	if plugins, ok := ctx.Value(ctxKey{}).(*Registry); ok {
		return plugins
	}
	return nil
}
