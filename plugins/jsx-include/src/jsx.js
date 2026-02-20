import * as reactTools from "react-tools";
import { renderToString } from "react-dom/server";
import React from 'react';
import * as Prelude from "./prelude";

function renderComponent(code, props = {}) {
    // Transform JSX to JavaScript
    const transformedCode = reactTools.transform(code, {}) + '; return App;';

    // Get global variables for execution context
    let [globalKeys, globalRefs] = getGlobals();

    // Add React to the context
    globalKeys.push('React');
    globalRefs.push(React);

    // Include code directly in the function body
    const Component = (new Function(...globalKeys, transformedCode))(...globalRefs);

    // Render the component to HTML
    return renderToString(React.createElement(Component, props, null));
}

function getGlobals() {
    const keys = Object
        .getOwnPropertyNames(Prelude)
        .filter(k => k !== "default")
        .sort();
    const refs = keys.map(k => Prelude[k]);
    return [keys, refs];
}

export { renderComponent };
