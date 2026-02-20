import { renderComponent } from './jsx';

export function render() {
    let input = {};

    try {
        const inputString = Host.inputString();
        if (inputString && inputString.trim()) {
            input = JSON.parse(inputString);
        }
    } catch (err) {
        throw new Error(`Failed to parse input: ${err.message}`);
    }

    let {
        script,
        props = {},
    } = input;

    if (!script || !script.trim()) {
        throw Error('The "script" param is empty or missing');
    }

    const html = renderComponent(script, props);
    Host.outputString(html);
}
