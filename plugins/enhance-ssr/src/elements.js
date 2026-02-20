import enhance from "@enhance/ssr"
import styleTransform from "@enhance/enhance-style-transform"

const Elements = {
    renderHtml(body, elements = {}, state = {}) {
        const elementFuncs = Elements.loadElements(elements);

        if (!elementFuncs || Object.keys(elementFuncs).length === 0) {
            return body;
        }

        const html = enhance({
            elements: elementFuncs,
            styleTransforms: [styleTransform],
            initialState: state || {},
        });

        return html`${body}`;
    },

    loadElements(elements) {
        const elementFuncs = {};

        if (!elements || typeof elements !== 'object') {
            return elementFuncs;
        }

        // elements is an object: { "element-name": "function code string", ... }
        Object.entries(elements).forEach(([name, code]) => {
            if (!name || !code) return;

            try {
                // Convert code string to function
                elementFuncs[name] = new Function("return " + code)();
            } catch (err) {
                throw new Error(`Error loading element "${name}"`, { cause: err });
            }
        });

        return elementFuncs;
    },
};

export default Elements;
