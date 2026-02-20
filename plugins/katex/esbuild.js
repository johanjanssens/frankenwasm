const esbuild = require('esbuild');

esbuild.build({
    entryPoints: ['src/index.js'],
    bundle: true,
    outfile: 'dist/index.js',
    format: 'cjs',
    platform: 'neutral',
    target: 'es2020',
    minify: true,
}).catch(() => process.exit(1));
