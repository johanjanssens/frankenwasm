import markdownit from 'markdown-it';

export function convert() {
    let input = Host.inputString();
    try {
        const parsed = JSON.parse(input);
        if (typeof parsed === 'string') {
            input = parsed;
        }
    } catch (e) {}
    const md = markdownit();
    const output = md.render(input);
    Host.outputString(output);
}
