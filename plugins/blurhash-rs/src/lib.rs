use extism_pdk::*;
use serde::Deserialize;

#[derive(Deserialize)]
struct EncodeInput {
    pixels: String, // base64-encoded raw RGBA pixels
    width: u32,
    height: u32,
    #[serde(default = "default_components_x")]
    components_x: u32,
    #[serde(default = "default_components_y")]
    components_y: u32,
}

fn default_components_x() -> u32 { 4 }
fn default_components_y() -> u32 { 3 }

#[plugin_fn]
pub fn encode(input: String) -> FnResult<String> {
    let input = match serde_json::from_str::<String>(&input) {
        Ok(s) => s,
        Err(_) => input,
    };

    let params: EncodeInput = serde_json::from_str(&input)
        .map_err(|e| Error::msg(format!("JSON parse error: {}", e)))?;

    let pixels = base64::Engine::decode(
        &base64::engine::general_purpose::STANDARD,
        &params.pixels,
    ).map_err(|e| Error::msg(format!("Base64 decode error: {}", e)))?;

    let hash = blurhash::encode(
        params.components_x,
        params.components_y,
        params.width,
        params.height,
        &pixels,
    ).map_err(|e| Error::msg(format!("Blurhash encode error: {}", e)))?;

    Ok(hash)
}

#[plugin_fn]
pub fn decode(input: String) -> FnResult<String> {
    let input = match serde_json::from_str::<String>(&input) {
        Ok(s) => s,
        Err(_) => input,
    };

    #[derive(Deserialize)]
    struct DecodeInput {
        hash: String,
        width: u32,
        height: u32,
    }

    let params: DecodeInput = serde_json::from_str(&input)
        .map_err(|e| Error::msg(format!("JSON parse error: {}", e)))?;

    let pixels = blurhash::decode(&params.hash, params.width, params.height, 1.0)
        .map_err(|e| Error::msg(format!("Blurhash decode error: {}", e)))?;

    let b64 = base64::Engine::encode(
        &base64::engine::general_purpose::STANDARD,
        &pixels,
    );

    let result = serde_json::json!({
        "pixels": b64,
        "width": params.width,
        "height": params.height,
    });

    Ok(result.to_string())
}
