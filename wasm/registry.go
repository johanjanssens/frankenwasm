package wasm

import (
	"context"
	"errors"
	"fmt"
	"sync"

	extism "github.com/extism/go-sdk"
)

var (
	ErrPluginNotFound   = errors.New("plugin not found")
	ErrFunctionNotFound = errors.New("function not found")
)

type registryEntry struct {
	name   string
	plugin *extism.Plugin
}

// Registry maintains an ordered collection of plugin instances with
// thread-safe concurrent access.
type Registry struct {
	plugins   []registryEntry
	pluginMap map[string]int // name -> index
	locks     sync.Map
	manager   *Manager
}

func newRegistry() *Registry {
	return &Registry{
		plugins:   make([]registryEntry, 0),
		pluginMap: make(map[string]int),
	}
}

// Call invokes a plugin's function with the provided arguments.
func (r *Registry) Call(ctx context.Context, name, function, args string) ([]byte, error) {
	plugin := r.Get(name)
	if plugin == nil {
		return nil, fmt.Errorf("%w: %s", ErrPluginNotFound, name)
	}

	if !plugin.FunctionExists(function) {
		return nil, fmt.Errorf("%w: %s", ErrFunctionNotFound, function)
	}

	lockVal, _ := r.locks.LoadOrStore(name, &sync.Mutex{})
	mutex := lockVal.(*sync.Mutex)

	mutex.Lock()
	_, output, err := plugin.CallWithContext(ctx, function, []byte(args))
	mutex.Unlock()

	if err != nil {
		return nil, err
	}

	return output, nil
}

// Get returns a plugin with the given name from the registry.
func (r *Registry) Get(name string) *extism.Plugin {
	idx, ok := r.pluginMap[name]
	if !ok {
		return nil
	}
	return r.plugins[idx].plugin
}

// Exists checks if a plugin with the given name exists in the registry.
func (r *Registry) Exists(name string) bool {
	_, ok := r.pluginMap[name]
	return ok
}

// Names returns a slice of all plugin names in registration order.
func (r *Registry) Names() []string {
	names := make([]string, len(r.plugins))
	for i, p := range r.plugins {
		names[i] = p.name
	}
	return names
}

// Metadata returns metadata for all loaded plugins.
func (r *Registry) Metadata() []Metadata {
	if r.manager != nil {
		return r.manager.Metadata()
	}
	return nil
}

// Len returns the number of plugins in the registry.
func (r *Registry) Len() int {
	return len(r.plugins)
}

// Clone creates a new registry by re-instantiating all plugins through the manager.
func (r *Registry) Clone(ctx context.Context) (*Registry, error) {
	if r.manager == nil {
		return newRegistry(), nil
	}

	registry, err := r.manager.InstantiateAll(ctx)
	if err != nil {
		return nil, fmt.Errorf("failed to clone registry: %w", err)
	}

	return registry, nil
}

// Close releases all plugins and their associated resources.
func (r *Registry) Close(ctx context.Context) error {
	var errs []error
	for _, p := range r.plugins {
		if err := p.plugin.Close(ctx); err != nil {
			errs = append(errs, fmt.Errorf("plugin %s: %w", p.name, err))
		}
	}
	return errors.Join(errs...)
}
