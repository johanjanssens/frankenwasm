/**
 * FrankenWASM Plugin PHP extension implementation
 * Object-oriented interface for WebAssembly plugin management
 */

#include <stdlib.h>
#include <stdio.h>

#include <php.h>
#include <php_ini.h>

#include <ext/json/php_json.h>

#include <Zend/zend_exceptions.h>
#include <Zend/zend_types.h>
#include <Zend/zend_hash.h>
#include <Zend/zend_smart_str.h>

#include "wasmplugin.h"
#include "phpext.h"

#include "phpext_cgo.h"

/* ============================================================================
 * STATIC VARIABLES & FORWARD DECLARATIONS
 * ============================================================================ */

static zend_class_entry *wasm_ce = NULL;
static zend_object_handlers wasm_object_handlers;

static inline wasm_object *wasm_from_obj(zend_object *obj);
static zend_object *wasm_create_object(zend_class_entry *ce);
static void wasm_free_object(zend_object *object);
static const zend_function_entry wasm_methods[];

/* ============================================================================
 * MODULE LIFECYCLE FUNCTIONS
 * ============================================================================ */

int frankenwasm_wasm_minit(void)
{
    zend_class_entry ce;

    INIT_NS_CLASS_ENTRY(ce, "FrankenPHP", "Wasm", wasm_methods);

    wasm_ce = zend_register_internal_class(&ce);

    if (UNEXPECTED(!wasm_ce)) {
        return FAILURE;
    }

    wasm_ce->ce_flags |= ZEND_ACC_FINAL;
    wasm_ce->create_object = wasm_create_object;

    memcpy(&wasm_object_handlers, zend_get_std_object_handlers(), sizeof(zend_object_handlers));
    wasm_object_handlers.offset = XtOffsetOf(wasm_object, std);
    wasm_object_handlers.free_obj = wasm_free_object;

    return SUCCESS;
}

/* ============================================================================
 * OBJECT LIFECYCLE FUNCTIONS
 * ============================================================================ */

static zend_object *wasm_create_object(zend_class_entry *ce)
{
    wasm_object *intern = ecalloc(1, sizeof(wasm_object) + zend_object_properties_size(ce));

    zend_object_std_init(&intern->std, ce);
    object_properties_init(&intern->std, ce);

    intern->name = NULL;
    intern->std.handlers = &wasm_object_handlers;

    return &intern->std;
}

static void wasm_free_object(zend_object *object)
{
    wasm_object *intern = wasm_from_obj(object);

    if (intern->name) {
        zend_string_release(intern->name);
    }

    zend_object_std_dtor(&intern->std);
}

/* ============================================================================
 * PHP METHOD IMPLEMENTATIONS
 * ============================================================================ */

PHP_METHOD(Wasm, __construct)
{
    zend_string *plugin_name;

    ZEND_PARSE_PARAMETERS_START(1, 1)
        Z_PARAM_STR(plugin_name)
    ZEND_PARSE_PARAMETERS_END();

    wasm_object *intern = wasm_from_obj(Z_OBJ_P(ZEND_THIS));

    if (UNEXPECTED(!go_wasm_exists(frankenphp_thread_index(), ZSTR_VAL(plugin_name)))) {
        frankenwasm_throw_exception("Plugin '%s' does not exist", ZSTR_VAL(plugin_name));
        RETURN_THROWS();
    }

    intern->name = zend_string_copy(plugin_name);
}

PHP_METHOD(Wasm, getName)
{
    ZEND_PARSE_PARAMETERS_NONE();

    wasm_object *intern = wasm_from_obj(Z_OBJ_P(ZEND_THIS));

    if (EXPECTED(intern->name)) {
        RETURN_STR_COPY(intern->name);
    }

    RETURN_NULL();
}

PHP_METHOD(Wasm, list)
{
    ZEND_PARSE_PARAMETERS_NONE();

    struct go_wasm_list_return result = go_wasm_list(frankenphp_thread_index());

    if (UNEXPECTED(!result.r1)) {
        if (result.r0) {
            frankenwasm_throw_exception("%s", result.r0);
            free(result.r0);
        } else {
            frankenwasm_throw_error("Unknown internal error in runtime");
        }
        RETURN_THROWS();
    }

    if (UNEXPECTED(result.r0 == NULL)) {
        RETURN_NULL();
    }

    zval decoded_result;
    ZVAL_UNDEF(&decoded_result);

    if (UNEXPECTED(php_json_decode_ex(&decoded_result, result.r0, strlen(result.r0),
                                       PHP_JSON_OBJECT_AS_ARRAY, FRANKENWASM_JSON_DEPTH) != SUCCESS)) {
        frankenwasm_throw_error("Failed to decode data");
        free(result.r0);
        RETURN_THROWS();
    }

    if (EXPECTED(Z_TYPE(decoded_result) == IS_ARRAY)) {
        free(result.r0);
        RETURN_ZVAL(&decoded_result, 1, 1);
    }

    free(result.r0);
    zval_ptr_dtor(&decoded_result);
    RETURN_NULL();
}

PHP_METHOD(Wasm, metadata)
{
    ZEND_PARSE_PARAMETERS_NONE();

    struct go_wasm_metadata_return result = go_wasm_metadata(frankenphp_thread_index());

    if (UNEXPECTED(!result.r1)) {
        if (result.r0) {
            frankenwasm_throw_exception("%s", result.r0);
            free(result.r0);
        } else {
            frankenwasm_throw_error("Unknown internal error in runtime");
        }
        RETURN_THROWS();
    }

    if (UNEXPECTED(result.r0 == NULL)) {
        RETURN_NULL();
    }

    zval decoded_result;
    ZVAL_UNDEF(&decoded_result);

    if (UNEXPECTED(php_json_decode_ex(&decoded_result, result.r0, strlen(result.r0),
                                       PHP_JSON_OBJECT_AS_ARRAY, FRANKENWASM_JSON_DEPTH) != SUCCESS)) {
        frankenwasm_throw_error("Failed to decode data");
        free(result.r0);
        RETURN_THROWS();
    }

    if (EXPECTED(Z_TYPE(decoded_result) == IS_ARRAY)) {
        free(result.r0);
        RETURN_ZVAL(&decoded_result, 1, 1);
    }

    free(result.r0);
    zval_ptr_dtor(&decoded_result);
    RETURN_NULL();
}

PHP_METHOD(Wasm, call)
{
    zend_string *function_name;
    zval *parameters = NULL;
    smart_str args_json = {0};

    ZEND_PARSE_PARAMETERS_START(1, 2)
        Z_PARAM_STR(function_name)
        Z_PARAM_OPTIONAL
        Z_PARAM_ZVAL(parameters)
    ZEND_PARSE_PARAMETERS_END();

    wasm_object *intern = wasm_from_obj(Z_OBJ_P(ZEND_THIS));

    if (UNEXPECTED(!intern->name)) {
        frankenwasm_throw_exception("Wasm object not properly initialized");
        RETURN_THROWS();
    }

    if (EXPECTED(parameters != NULL)) {
        php_json_encode(&args_json, parameters, PHP_JSON_THROW_ON_ERROR);
        smart_str_0(&args_json);
    } else {
        smart_str_appendl(&args_json, "{}", 2);
        smart_str_0(&args_json);
    }

    struct go_wasm_call_return result = go_wasm_call(
        frankenphp_thread_index(),
        ZSTR_VAL(intern->name),
        ZSTR_VAL(function_name),
        ZSTR_VAL(args_json.s)
    );

    smart_str_free(&args_json);

    if (UNEXPECTED(!result.r1)) {
        if (result.r0) {
            frankenwasm_throw_exception("%s", result.r0);
            free(result.r0);
        } else {
            frankenwasm_throw_error("Unknown internal error in runtime");
        }
        RETURN_THROWS();
    }

    if (UNEXPECTED(result.r0 == NULL)) {
        RETURN_NULL();
    }

    zval decoded_result;
    ZVAL_UNDEF(&decoded_result);

    zend_try {
        if (EXPECTED(php_json_decode_ex(&decoded_result, result.r0, strlen(result.r0),
                                         PHP_JSON_OBJECT_AS_ARRAY, FRANKENWASM_JSON_DEPTH) == SUCCESS)) {
            RETURN_ZVAL(&decoded_result, 1, 1);
            zval_ptr_dtor(&decoded_result);
        } else {
            RETVAL_STRING(result.r0);
        }

        free(result.r0);

    } zend_catch {
        free(result.r0);
        zend_bailout();
        RETURN_THROWS();

    } zend_end_try();
}

/* ============================================================================
 * METHOD TABLE
 * ============================================================================ */

static const zend_function_entry wasm_methods[] = {
    PHP_ME(Wasm, __construct, arginfo_wasm_construct, ZEND_ACC_PUBLIC | ZEND_ACC_CTOR)
    PHP_ME(Wasm, getName, arginfo_wasm_get_name, ZEND_ACC_PUBLIC)
    PHP_ME(Wasm, list, arginfo_wasm_list, ZEND_ACC_PUBLIC | ZEND_ACC_STATIC)
    PHP_ME(Wasm, metadata, arginfo_wasm_metadata, ZEND_ACC_PUBLIC | ZEND_ACC_STATIC)
    PHP_ME(Wasm, call, arginfo_wasm_call, ZEND_ACC_PUBLIC)
    PHP_FE_END
};

/* ============================================================================
 * HELPER FUNCTIONS
 * ============================================================================ */

static inline wasm_object *wasm_from_obj(zend_object *obj) {
    return (wasm_object *)((char *)(obj) - XtOffsetOf(wasm_object, std));
}
