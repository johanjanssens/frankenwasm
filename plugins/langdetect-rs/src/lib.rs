use extism_pdk::*;

#[plugin_fn]
pub fn detect(input: String) -> FnResult<String> {
    let input = match serde_json::from_str::<String>(&input) {
        Ok(s) => s,
        Err(_) => input,
    };

    let info = whatlang::detect(&input);

    let result = match info {
        Some(info) => {
            serde_json::json!({
                "language": format!("{:?}", info.lang()),
                "language_code": info.lang().code(),
                "language_name": info.lang().eng_name(),
                "script": format!("{:?}", info.script()),
                "confidence": info.confidence(),
                "is_reliable": info.is_reliable(),
            })
        }
        None => {
            serde_json::json!({
                "language": null,
                "error": "Could not detect language"
            })
        }
    };

    Ok(result.to_string())
}
