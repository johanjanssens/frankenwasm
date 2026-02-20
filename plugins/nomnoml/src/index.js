const nomnoml = require('nomnoml');

function render() {
    const input = JSON.parse(Host.inputString());

    if (!input.code) {
        throw new Error("The 'code' param is required");
    }

    const svg = nomnoml.renderSvg(input.code);
    Host.outputString(svg);
}

module.exports = { render };
