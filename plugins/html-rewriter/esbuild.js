const esbuild = require('esbuild');
const { NodeModulesPolyfillPlugin } = require('@esbuild-plugins/node-modules-polyfill')

esbuild
    .build({
        entryPoints: ['src/index.js'],
        outdir: 'dist',
        bundle: true,
        sourcemap: true,
        minify: false,
        format: 'cjs',
        target: ['es2020'],
        plugins: [NodeModulesPolyfillPlugin()]
    })
