use extism_pdk::*;
use serde_json::Value as JsonValue;

#[plugin_fn]
pub fn parse(input: String) -> FnResult<String> {
    // The PHP extension JSON-encodes string args, so unwrap if needed
    let input = match serde_json::from_str::<String>(&input) {
        Ok(s) => s,
        Err(_) => input,
    };

    let value: toml::Value = toml::from_str(&input)
        .map_err(|e| Error::msg(format!("TOML parse error: {}", e)))?;

    let json_value = toml_to_json(value);
    let json_str = serde_json::to_string_pretty(&json_value)
        .map_err(|e| Error::msg(format!("JSON serialize error: {}", e)))?;

    Ok(json_str)
}

#[plugin_fn]
pub fn serialize(input: String) -> FnResult<String> {
    // JSON-unwrap string args from the PHP extension
    let input = match serde_json::from_str::<String>(&input) {
        Ok(s) => s,
        Err(_) => input,
    };

    let json_value: JsonValue = serde_json::from_str(&input)
        .map_err(|e| Error::msg(format!("JSON parse error: {}", e)))?;

    let toml_value = json_to_toml(json_value);
    let toml_str = toml::to_string_pretty(&toml_value)
        .map_err(|e| Error::msg(format!("TOML serialize error: {}", e)))?;

    Ok(toml_str)
}

fn toml_to_json(value: toml::Value) -> JsonValue {
    match value {
        toml::Value::String(s) => JsonValue::String(s),
        toml::Value::Integer(i) => serde_json::json!(i),
        toml::Value::Float(f) => serde_json::json!(f),
        toml::Value::Boolean(b) => JsonValue::Bool(b),
        toml::Value::Datetime(dt) => JsonValue::String(dt.to_string()),
        toml::Value::Array(arr) => JsonValue::Array(arr.into_iter().map(toml_to_json).collect()),
        toml::Value::Table(table) => {
            let map: serde_json::Map<String, JsonValue> = table
                .into_iter()
                .map(|(k, v)| (k, toml_to_json(v)))
                .collect();
            JsonValue::Object(map)
        }
    }
}

fn json_to_toml(value: JsonValue) -> toml::Value {
    match value {
        JsonValue::Null => toml::Value::String("null".to_string()),
        JsonValue::Bool(b) => toml::Value::Boolean(b),
        JsonValue::Number(n) => {
            if let Some(i) = n.as_i64() {
                toml::Value::Integer(i)
            } else if let Some(f) = n.as_f64() {
                toml::Value::Float(f)
            } else {
                toml::Value::String(n.to_string())
            }
        }
        JsonValue::String(s) => toml::Value::String(s),
        JsonValue::Array(arr) => toml::Value::Array(arr.into_iter().map(json_to_toml).collect()),
        JsonValue::Object(map) => {
            let table: toml::map::Map<String, toml::Value> = map
                .into_iter()
                .map(|(k, v)| (k, json_to_toml(v)))
                .collect();
            toml::Value::Table(table)
        }
    }
}
