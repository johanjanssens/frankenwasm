/**
 * FrankenWASM PHP Extension - Main Module
 * Core module initialization and utility functions
 */

#include <stdarg.h>

#include <php.h>
#include <php_ini.h>

#include <ext/standard/info.h>

#include <Zend/zend_exceptions.h>

#include "wasmplugin.h"

#include "phpext.h"
#include "phpext_cgo.h"

/* ============================================================================
 * UTILITY FUNCTIONS
 * ============================================================================ */

void frankenwasm_throw_exception(const char *format, ...) {
    va_list args;
    va_start(args, format);

    zend_string *message = zend_vstrpprintf(0, format, args);
    va_end(args);

    zend_throw_exception(zend_ce_exception, ZSTR_VAL(message), 0);
    zend_string_release(message);
}

void frankenwasm_throw_error(const char *format, ...) {
    va_list args;
    va_start(args, format);

    zend_string *message = zend_vstrpprintf(0, format, args);
    va_end(args);

    zend_throw_exception_ex(zend_ce_error, E_ERROR, "%s", ZSTR_VAL(message));
    zend_string_release(message);
}

/* ============================================================================
 * MODULE LIFECYCLE FUNCTIONS
 * ============================================================================ */

#ifdef COMPILE_DL_FRANKENWASM
ZEND_GET_MODULE(frankenwasm)
#endif

PHP_MINFO_FUNCTION(frankenwasm)
{
    php_info_print_table_start();
    php_info_print_table_header(2, "FrankenWASM Support", "enabled");
    php_info_print_table_row(2, "Version", "0.1.0");
    php_info_print_table_end();
}

zend_module_entry frankenwasm_module_entry = {
    STANDARD_MODULE_HEADER,
    "frankenwasm",
    frankenwasm_functions,
    frankenwasm_minit,
    frankenwasm_mshutdown,
    frankenwasm_rinit,
    frankenwasm_rshutdown,
    PHP_MINFO(frankenwasm),
    "0.1.0",
    STANDARD_MODULE_PROPERTIES
};

int frankenwasm_minit(int type, int module_number) {
    if (frankenwasm_wasm_minit() != SUCCESS) {
        php_error(E_WARNING, "Failed to register FrankenPHP\\Wasm class.");
        return FAILURE;
    }

    return SUCCESS;
}

int frankenwasm_rinit(int type, int module_number) {
    return SUCCESS;
}

int frankenwasm_rshutdown(int type, int module_number) {
    return SUCCESS;
}

int frankenwasm_mshutdown(int type, int module_number) {
    return SUCCESS;
}

/* ============================================================================
 * FUNCTION TABLE
 * ============================================================================ */

const zend_function_entry frankenwasm_functions[] = {
    ZEND_FE_END
};
