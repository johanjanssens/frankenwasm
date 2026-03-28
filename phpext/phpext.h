#ifndef FRANKENWASM_H
#define FRANKENWASM_H

#include <stddef.h>
#include <stdint.h>
#include <stdbool.h>

#include <php.h>
#include <Zend/zend_types.h>

#include "wasmplugin.h"

#define FRANKENWASM_VERSION "0.1.0"
#define FRANKENWASM_JSON_DEPTH 512

// Module entry (registered via frankenphp.RegisterExtension)
extern zend_module_entry frankenwasm_module_entry;

// Module lifecycle functions
int frankenwasm_minit(int type, int module_number);
int frankenwasm_mshutdown(int type, int module_number);
int frankenwasm_rinit(int type, int module_number);
int frankenwasm_rshutdown(int type, int module_number);

// Utility functions
void frankenwasm_throw_exception(const char *format, ...);
void frankenwasm_throw_error(const char *format, ...);

// PHP function declarations
extern const zend_function_entry frankenwasm_functions[];

#endif // FRANKENWASM_H
