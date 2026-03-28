/**
 * FrankenWASM Plugin PHP extension
 * Object-oriented interface for WebAssembly plugin management
 */

#ifndef FRANKENWASM_WASMPLUGIN_H
#define FRANKENWASM_WASMPLUGIN_H

#include <php.h>
#include <Zend/zend_types.h>
#include <Zend/zend_API.h>

/**
 * Wasm object structure
 * Contains the plugin name for all operations
 */
typedef struct {
    zend_string *name;
    zend_object std;
} wasm_object;

#define Z_WASM_OBJ_P(zv) wasm_from_obj(Z_OBJ_P(zv))

int frankenwasm_wasm_minit(void);

PHP_METHOD(Wasm, __construct);
PHP_METHOD(Wasm, getName);
PHP_METHOD(Wasm, call);
PHP_METHOD(Wasm, list);
PHP_METHOD(Wasm, metadata);

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_construct, 0, 0, 1)
    ZEND_ARG_TYPE_INFO(0, name, IS_STRING, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_get_name, 0, 0, IS_STRING, 1)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_list, 0, 0, IS_ARRAY, 1)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_metadata, 0, 0, IS_ARRAY, 1)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_MASK_EX(arginfo_wasm_call, 0, 1, MAY_BE_STRING | MAY_BE_ARRAY | MAY_BE_NULL)
    ZEND_ARG_TYPE_INFO(0, function, IS_STRING, 0)
    ZEND_ARG_TYPE_INFO_WITH_DEFAULT_VALUE(0, parameters, IS_MIXED, 0, "[]")
ZEND_END_ARG_INFO()

#endif /* FRANKENWASM_WASMPLUGIN_H */
