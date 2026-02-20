const esbuild = require('esbuild');
const path = require('path');

esbuild.build({
    entryPoints: ['src/index.js'],
    bundle: true,
    outfile: 'dist/index.js',
    format: 'cjs',
    target: 'es2020',
    mainFields: ['main', 'module'],
    minify: true,
    alias: {
        'fs': path.resolve(__dirname, 'shims/fs.js'),
        'path': path.resolve(__dirname, 'shims/path.js'),
        'url': path.resolve(__dirname, 'shims/url.js'),
    },
}).catch(() => process.exit(1));
