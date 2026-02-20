use extism_pdk::*;
use serde_json::Value;
use serde::Deserialize;
use jsonpath_rust::*;

#[derive(Deserialize, FromBytes)]
#[encoding(Json)]
struct SelectInput {
    data: Option<Value>,
    url: Option<String>,
    query: String,
}

#[plugin_fn]
pub fn select(input: SelectInput) -> FnResult<String> {
    let data = if let Some(url) = input.url {
        if url.starts_with("http://") || url.starts_with("https://") {
            let req = HttpRequest::new(url);
            let res = http::request::<()>(&req, None)?;
            match serde_json::from_slice::<Value>(&res.body()) {
                Ok(json_data) => json_data,
                Err(_) => return Ok(String::from("Failed to parse JSON from HTTP response"))
            }
        } else {
            return Ok(String::from("URL must start with http:// or https://"));
        }
    } else if let Some(data) = input.data {
        data
    } else {
        return Ok(String::from("Either 'data' or 'url' must be provided"));
    };

    let results: Vec<&Value> = match data.query(&input.query) {
        Ok(values) => values,
        Err(e) => return Ok(format!("Failed to execute JSONPath query: {}", e))
    };

    match serde_json::to_string_pretty(&results) {
        Ok(json_str) => Ok(json_str),
        Err(_) => Ok(String::from("Failed to serialize results to JSON"))
    }
}
