import sanitizeHtml from 'sanitize-html';

export function sanitize() {
    const input = Host.inputString();

    // Try JSON unwrap (the PHP extension JSON-encodes string args)
    let html = input;
    try {
        const parsed = JSON.parse(input);
        if (typeof parsed === 'string') html = parsed;
    } catch (e) {}

    const clean = sanitizeHtml(html, {
        allowedTags: sanitizeHtml.defaults.allowedTags.concat(['img', 'h1', 'h2']),
        allowedAttributes: {
            ...sanitizeHtml.defaults.allowedAttributes,
            img: ['src', 'alt', 'width', 'height'],
        },
    });

    Host.outputString(clean);
}
