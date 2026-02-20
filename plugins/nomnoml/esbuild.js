const esbuild = require('esbuild');

esbuild.build({
    entryPoints: ['src/index.js'],
    bundle: true,
    outfile: 'dist/index.js',
    format: 'cjs',
    target: 'es2020',
    mainFields: ['main', 'module'],
    minify: true,
    external: ['fs', 'path'],
}).catch(() => process.exit(1));
