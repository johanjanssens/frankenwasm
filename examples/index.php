<?php
use FrankenPHP\Wasm;

$plugins = Wasm::list();
$_meta = Wasm::metadata();
$_sizes = [];
foreach ($_meta as $_m) {
    $_sizes[$_m['name']] = $_m['file_size'];
}

function _fmtSize($b) {
    if ($b >= 1048576) return number_format($b / 1048576, 1) . ' MB';
    if ($b >= 1024) return number_format($b / 1024, 0) . ' KB';
    return $b . ' B';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>(function(){var t=localStorage.getItem('theme'),d=window.matchMedia('(prefers-color-scheme:dark)').matches;if(t==='dark'||(!t&&d))document.documentElement.classList.add('dark')})()</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FrankenWASM Demos</title>
    <link rel="stylesheet" href="/style.php">
    <style>
        body { padding: 40px 20px; }
        .header {
            max-width: 1200px;
            margin: 0 auto 40px;
            text-align: center;
            position: relative;
        }
        .header h1 { font-size: 2.5rem; color: #1a1a2e; margin-bottom: 8px; }
        .header p { font-size: 1.1rem; color: #666; }
        .header .theme-toggle { position: absolute; top: 0; right: 0; }
        .plugins-bar {
            max-width: 1200px;
            margin: 0 auto 30px;
            background: white;
            border-radius: 8px;
            padding: 16px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .plugins-bar .label { font-weight: 600; color: #444; font-size: 0.9rem; }
        .plugin-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #e8f4fd;
            color: #1976d2;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .plugin-badge.missing { background: #fce4ec; color: #c62828; }
        .grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 24px;
        }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
        .card-accent { height: 6px; }
        .card-body { padding: 24px; }
        .card-body h2 { font-size: 1.3rem; margin-bottom: 8px; color: #1a1a2e; }
        .card-body p { font-size: 0.95rem; color: #666; margin-bottom: 16px; }
        .card-tags { display: flex; gap: 8px; flex-wrap: wrap; }
        .tag {
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .tag-go { background: #e0f2f1; color: #00695c; }
        .tag-rust { background: #fbe9e7; color: #bf360c; }
        .tag-js { background: #fff8e1; color: #f57f17; }
        .tag-multi { background: #ede7f6; color: #4527a0; }
        .tag-size { background: #f3f4f6; color: #6b7280; margin-left: auto; }
        @media (max-width: 768px) {
            .grid { grid-template-columns: 1fr; }
            .header h1 { font-size: 2rem; }
        }
        html.dark .header h1 { color: #e0e0e0; }
        html.dark .header p { color: #aaa; }
        html.dark .plugins-bar { background: #16213e; box-shadow: 0 2px 8px rgba(0,0,0,0.3); }
        html.dark .plugins-bar .label { color: #ccc; }
        html.dark .plugin-badge { background: #1e2a45; color: #64b5f6; }
        html.dark .plugin-badge.missing { background: #3e1a1a; color: #ef9a9a; }
        html.dark .card { background: #16213e; box-shadow: 0 2px 12px rgba(0,0,0,0.3); }
        html.dark .card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.4); }
        html.dark .card-body h2 { color: #e0e0e0; }
        html.dark .card-body p { color: #aaa; }
        html.dark .tag-go { background: #1a332e; color: #4db6ac; }
        html.dark .tag-rust { background: #332119; color: #ffab91; }
        html.dark .tag-js { background: #33301a; color: #ffd54f; }
        html.dark .tag-multi { background: #1f1a33; color: #b39ddb; }
        html.dark .tag-size { background: #1e2a45; color: #8b95a5; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FrankenWASM</h1>
        <p>WebAssembly plugin demos powered by FrankenPHP + Extism</p>
        <button class="theme-toggle" onclick="document.documentElement.classList.toggle('dark');localStorage.setItem('theme',document.documentElement.classList.contains('dark')?'dark':'light')" aria-label="Toggle theme">&#x25D1;</button>
    </div>

    <div class="plugins-bar">
        <span class="label">Loaded Plugins:</span>
        <?php
        $allDemoPlugins = ['markdown','markdown-rs','markdown-js','jsonpath-rs','chroma','minify','enhance-ssr','jsx-include','html-rewriter','qrcode','toml-rs','katex','nomnoml','sanitize','sanitize-rs','sanitize-js','scss-rs','blurhash-rs','ascii-rs','langdetect-rs'];
        foreach ($allDemoPlugins as $name):
            $loaded = in_array($name, $plugins);
        ?>
            <span class="plugin-badge <?= $loaded ? '' : 'missing' ?>"><?= $name ?></span>
        <?php endforeach; ?>
    </div>

    <div class="grid">
        <a href="markdown/" class="card">
            <div class="card-accent" style="background: linear-gradient(90deg, #00ADD8, #DEA584, #F7DF1E)"></div>
            <div class="card-body">
                <h2>Markdown</h2>
                <p>Compare markdown rendering across Go, Rust, and JavaScript implementations side-by-side with performance timing.</p>
                <div class="card-tags">
                    <span class="tag tag-go">Go</span>
                    <span class="tag tag-rust">Rust</span>
                    <span class="tag tag-js">JS</span>
                    <?php $mdTotal = ($_sizes['markdown'] ?? 0) + ($_sizes['markdown-rs'] ?? 0) + ($_sizes['markdown-js'] ?? 0); ?>
                    <?php if ($mdTotal): ?><span class="tag tag-size"><?= _fmtSize($mdTotal) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="highlight/" class="card">
            <div class="card-accent" style="background: #00ADD8"></div>
            <div class="card-body">
                <h2>Syntax Highlighting</h2>
                <p>Code syntax highlighting with Chroma. Supports configurable themes, line numbers, and multiple languages.</p>
                <div class="card-tags">
                    <span class="tag tag-go">Go</span>
                    <?php if (isset($_sizes['chroma'])): ?><span class="tag tag-size"><?= _fmtSize($_sizes['chroma']) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="jsonpath/" class="card">
            <div class="card-accent" style="background: #DEA584"></div>
            <div class="card-body">
                <h2>JSONPath</h2>
                <p>Query JSON data with JSONPath expressions. Supports inline data and HTTP URL sources with performance benchmarks.</p>
                <div class="card-tags">
                    <span class="tag tag-rust">Rust</span>
                    <?php if (isset($_sizes['jsonpath-rs'])): ?><span class="tag tag-size"><?= _fmtSize($_sizes['jsonpath-rs']) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="minify/" class="card">
            <div class="card-accent" style="background: #00ADD8"></div>
            <div class="card-body">
                <h2>Minify</h2>
                <p>Minify CSS, JavaScript, HTML, and SVG with size reduction metrics. Powered by tdewolff/minify.</p>
                <div class="card-tags">
                    <span class="tag tag-go">Go</span>
                    <?php if (isset($_sizes['minify'])): ?><span class="tag tag-size"><?= _fmtSize($_sizes['minify']) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="enhance-ssr/" class="card">
            <div class="card-accent" style="background: #F7DF1E"></div>
            <div class="card-body">
                <h2>Enhance SSR</h2>
                <p>Server-side rendering of custom HTML elements with scoped styles using @enhance/ssr.</p>
                <div class="card-tags">
                    <span class="tag tag-js">JS</span>
                    <?php if (isset($_sizes['enhance-ssr'])): ?><span class="tag tag-size"><?= _fmtSize($_sizes['enhance-ssr']) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="jsx-include/" class="card">
            <div class="card-accent" style="background: #F7DF1E"></div>
            <div class="card-body">
                <h2>JSX / React SSR</h2>
                <p>Server-side render React JSX components with props, directly from PHP. Inline JSX code compiled and rendered via WASM.</p>
                <div class="card-tags">
                    <span class="tag tag-js">JS</span>
                    <?php if (isset($_sizes['jsx-include'])): ?><span class="tag tag-size"><?= _fmtSize($_sizes['jsx-include']) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="html-rewriter/" class="card">
            <div class="card-accent" style="background: #F7DF1E"></div>
            <div class="card-body">
                <h2>HTML Rewriter</h2>
                <p>Transform HTML using inline JavaScript transformer functions with cheerio-powered DOM manipulation.</p>
                <div class="card-tags">
                    <span class="tag tag-js">JS</span>
                    <?php if (isset($_sizes['html-rewriter'])): ?><span class="tag tag-size"><?= _fmtSize($_sizes['html-rewriter']) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="qrcode/" class="card">
            <div class="card-accent" style="background: #00ADD8"></div>
            <div class="card-body">
                <h2>QR Code Generator</h2>
                <p>Generate QR codes as SVG from text, URLs, WiFi configs, and vCards. No PHP extension needed.</p>
                <div class="card-tags">
                    <span class="tag tag-go">Go</span>
                    <?php if (isset($_sizes['qrcode'])): ?><span class="tag tag-size"><?= _fmtSize($_sizes['qrcode']) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="toml/" class="card">
            <div class="card-accent" style="background: #DEA584"></div>
            <div class="card-body">
                <h2>TOML Parser</h2>
                <p>Parse TOML to JSON and serialize JSON to TOML. Fills a gap in PHP which has no native TOML support.</p>
                <div class="card-tags">
                    <span class="tag tag-rust">Rust</span>
                    <?php if (isset($_sizes['toml-rs'])): ?><span class="tag tag-size"><?= _fmtSize($_sizes['toml-rs']) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="katex/" class="card">
            <div class="card-accent" style="background: #F7DF1E"></div>
            <div class="card-body">
                <h2>KaTeX Math</h2>
                <p>Render LaTeX math expressions to HTML server-side. Quadratic formulas, integrals, matrices and more.</p>
                <div class="card-tags">
                    <span class="tag tag-js">JS</span>
                    <?php if (isset($_sizes['katex'])): ?><span class="tag tag-size"><?= _fmtSize($_sizes['katex']) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="diagrams/" class="card">
            <div class="card-accent" style="background: #F7DF1E"></div>
            <div class="card-body">
                <h2>Diagrams</h2>
                <p>Render UML class diagrams, flow charts, and architecture diagrams from text notation via nomnoml.</p>
                <div class="card-tags">
                    <span class="tag tag-js">JS</span>
                    <?php if (isset($_sizes['nomnoml'])): ?><span class="tag tag-size"><?= _fmtSize($_sizes['nomnoml']) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="sanitize/" class="card">
            <div class="card-accent" style="background: linear-gradient(90deg, #00ADD8, #DEA584, #F7DF1E)"></div>
            <div class="card-body">
                <h2>HTML Sanitize</h2>
                <p>Compare HTML sanitization across Go (bluemonday), Rust (ammonia), and JS (sanitize-html) side-by-side.</p>
                <div class="card-tags">
                    <span class="tag tag-go">Go</span>
                    <span class="tag tag-rust">Rust</span>
                    <span class="tag tag-js">JS</span>
                    <?php $sanTotal = ($_sizes['sanitize'] ?? 0) + ($_sizes['sanitize-rs'] ?? 0) + ($_sizes['sanitize-js'] ?? 0); ?>
                    <?php if ($sanTotal): ?><span class="tag tag-size"><?= _fmtSize($sanTotal) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="scss/" class="card">
            <div class="card-accent" style="background: #CD6799"></div>
            <div class="card-body">
                <h2>SCSS Compiler</h2>
                <p>Compile SCSS/Sass to CSS with variables, nesting, mixins, and functions. Powered by the grass crate.</p>
                <div class="card-tags">
                    <span class="tag tag-rust">Rust</span>
                    <?php if (isset($_sizes['scss-rs'])): ?><span class="tag tag-size"><?= _fmtSize($_sizes['scss-rs']) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="blurhash/" class="card">
            <div class="card-accent" style="background: #8B5CF6"></div>
            <div class="card-body">
                <h2>Blurhash</h2>
                <p>Generate compact blur placeholder strings from images and decode them back. Great for lazy-loading UX.</p>
                <div class="card-tags">
                    <span class="tag tag-rust">Rust</span>
                    <?php if (isset($_sizes['blurhash-rs'])): ?><span class="tag tag-size"><?= _fmtSize($_sizes['blurhash-rs']) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="ascii/" class="card">
            <div class="card-accent" style="background: #00ff41"></div>
            <div class="card-body">
                <h2>ASCII Art</h2>
                <p>Generate FIGlet-style text banners with multiple fonts. Terminal-style text art from any string.</p>
                <div class="card-tags">
                    <span class="tag tag-rust">Rust</span>
                    <?php if (isset($_sizes['ascii-rs'])): ?><span class="tag tag-size"><?= _fmtSize($_sizes['ascii-rs']) ?></span><?php endif; ?>
                </div>
            </div>
        </a>

        <a href="langdetect/" class="card">
            <div class="card-accent" style="background: #6366f1"></div>
            <div class="card-body">
                <h2>Language Detection</h2>
                <p>Detect 60+ languages from text with confidence scores. Lightning-fast using the whatlang crate.</p>
                <div class="card-tags">
                    <span class="tag tag-rust">Rust</span>
                    <?php if (isset($_sizes['langdetect-rs'])): ?><span class="tag tag-size"><?= _fmtSize($_sizes['langdetect-rs']) ?></span><?php endif; ?>
                </div>
            </div>
        </a>
    </div>

    <footer>hack'd by <a href="https://bsky.app/profile/johanjanssens.bsky.social" target="_blank">Johan Janssens</a></footer>
</body>
</html>
