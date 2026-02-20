let cheerio;

const Transformations = {
    transformHtml(html, transformerFuncs = {}, props = {}) {
        if (!html || !html.trim() || !transformerFuncs || Object.keys(transformerFuncs).length === 0) {
            return;
        }

        if (!cheerio) {
            cheerio = require('cheerio/slim');
        }

        const $ = cheerio.load(html, { xml: { xmlMode: false } }, false);

        Object.entries(transformerFuncs).forEach(([name, transformerFunc]) => {
            transformerFunc($, props);
        });

        return $.html();
    },

    loadTransformations(transformations) {
        const transformationFuncs = {};

        if (!transformations || transformations.length === 0) {
            return transformationFuncs;
        }

        transformations.forEach((script, index) => {
            if (!script || !script.trim()) {
                return;
            }

            try {
                transformationFuncs[index] = this.createTransformerFunc(script);
            } catch (err) {
                throw new Error(`Error loading transformation`, { cause: err });
            }
        });

        return transformationFuncs;
    },

    createTransformerFunc(script) {
        const context = {};

        const scriptFn = new Function('$', 'props', 'context', `
        ${script}

        if (typeof Transformer !== 'function') {
            throw new Error('No "Transformer" function defined in script');
        }

        context.Transformer = Transformer;
    `);

        return function ($, props) {
            scriptFn($, props, context);

            if (typeof context.Transformer !== 'function') {
                throw new Error('No "Transformer" function defined in script');
            }

            return context.Transformer($, props);
        };
    }
};

export default Transformations;
