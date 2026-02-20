use extism_pdk::*;

#[plugin_fn]
pub fn convert(text: String) -> FnResult<String> {
    // Try JSON unwrap (input may be JSON-encoded string wrapped in quotes)
    let input = match serde_json::from_str::<String>(&text) {
        Ok(unwrapped) => unwrapped,
        Err(_) => text,
    };

    let mut options = markdown::Options::gfm();
    options.compile.allow_dangerous_html = true;
    options.compile.allow_dangerous_protocol = true;

    let result = markdown::to_html_with_options(&input, &options)
        .unwrap_or_else(|_| input.clone());

    Ok(result)
}
