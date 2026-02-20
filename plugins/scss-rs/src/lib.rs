use extism_pdk::*;

#[plugin_fn]
pub fn compile(input: String) -> FnResult<String> {
    let input = match serde_json::from_str::<String>(&input) {
        Ok(s) => s,
        Err(_) => input,
    };

    let css = grass::from_string(input, &grass::Options::default())
        .map_err(|e| Error::msg(format!("SCSS compile error: {}", e)))?;

    Ok(css)
}
