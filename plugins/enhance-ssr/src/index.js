import Elements from './elements';

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
        html,
        elements = {},
        state = {},
    } = input;

    if (!html || !html.trim()) {
        throw Error('The "html" param is empty or missing');
    }

    const output = Elements.renderHtml(html, elements, state);

    const bodyMatch = output.match(/<body.*?>([\s\S]*)<\/body>/);
    const bodyContent = bodyMatch ? bodyMatch[1] : '';

    const styleMatch = output.match(/<head\b[^>]*>[\s\S]*?<style.*?>([\s\S]*?)<\/style>[\s\S]*?<\/head>/);
    const styleContent = styleMatch ? styleMatch[1] : '';

    Host.outputString(`<style>\n${styleContent}\n</style>\n\n${bodyContent}`);
}
