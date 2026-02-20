use extism_pdk::*;

#[plugin_fn]
pub fn sanitize(input: String) -> FnResult<String> {
    // JSON-unwrap string args from the PHP extension
    let input = match serde_json::from_str::<String>(&input) {
        Ok(s) => s,
        Err(_) => input,
    };
    let sanitized = ammonia::clean(&input);
    Ok(sanitized)
}
