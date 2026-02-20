import Transformations from './transformations';

export function transform() {
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
        transformations = [],
        props = {}
    } = input;

    if (!html || !html.trim()) {
        throw Error('The "html" param is empty or missing');
    }

    const transformerFuncs = Transformations.loadTransformations(transformations);
    const output = Transformations.transformHtml(html, transformerFuncs, props);

    if (output) {
        Host.outputString(output);
    }
}
