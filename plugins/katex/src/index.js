const katex = require('katex');

function render() {
    const input = JSON.parse(Host.inputString());

    if (!input.expression) {
        throw new Error("The 'expression' param is required");
    }

    const html = katex.renderToString(input.expression, {
        displayMode: input.displayMode !== false,
        throwOnError: false,
        output: 'html',
    });

    Host.outputString(html);
}

module.exports = { render };
