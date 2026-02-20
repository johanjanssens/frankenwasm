<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'Minify';
$_prev = ['url' => '../jsonpath/', 'label' => 'JSONPath'];
$_next = ['url' => '../enhance-ssr/', 'label' => 'Enhance SSR'];
$_styleExtra = <<<'CSS'
        .card {
            max-width: 900px;
            margin: 0 auto 24px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 24px;
            overflow: hidden;
        }
        .card.css { border-left: 5px solid #3498db; }
        .card.js { border-left: 5px solid #f1c40f; }
        .card.html { border-left: 5px solid #e74c3c; }
        .card.svg { border-left: 5px solid #2ecc71; }
        .card h3 { margin-bottom: 12px; color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 8px; }
        .stats {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .stats div { background: #f8f9fa; padding: 4px 10px; border-radius: 4px; }
        .stats span { font-weight: bold; }
        .code-section { margin-bottom: 12px; position: relative; padding-left: 70px; }
        .code-label { position: absolute; left: 0; top: 12px; font-weight: bold; font-size: 13px; color: #444; }
        pre.fn-call { background: #f0f8ff; border-color: #d0e3f0; }
        html.dark .card { background: #16213e; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        html.dark .card h3 { color: #e0e0e0; border-bottom-color: #2a3a5c; }
        html.dark .stats div { background: #1e2a45; color: #ccc; }
        html.dark .code-label { color: #ccc; }
        html.dark pre.fn-call { background: #1e2a45; border-color: #2a3a5c; }
        .card pre { max-height: 200px; overflow-y: auto; }
CSS;
include __DIR__ . '/../_header.php';
?>

    <div class="intro">
        <div class="intro-badges">
            <span class="badge badge-go">Go</span>
            <?php if (isset($_sizes['minify'])): ?><span class="badge badge-size"><?= _fmtSize($_sizes['minify']) ?></span><?php endif; ?>
        </div>
        <h1>Minification</h1>
        <p>Minify CSS, JS, HTML, and SVG via <code>$min->call('css|js|html|svg', $input)</code></p>
    </div>

    <?php
    $min = new Wasm('minify');
    $maxChars = 500;

    function showMinify($min, $type, $title, $input, $maxChars) {
        $start = microtime(true);
        $result = $min->call($type, $input);
        $time = (microtime(true) - $start) * 1000;
        $origSize = strlen($input);
        $resultSize = strlen($result);
        $reduction = $origSize > 0 ? round(100 - ($resultSize * 100 / $origSize), 1) : 0;

        echo '<div class="card ' . $type . '">';
        echo '<h3>' . htmlspecialchars($title) . '</h3>';
        echo '<div class="stats">';
        echo '<div>Time: <span>' . number_format($time, 2) . ' ms</span></div>';
        echo '<div>Original: <span>' . number_format($origSize) . ' bytes</span></div>';
        echo '<div>Minified: <span>' . number_format($resultSize) . ' bytes</span></div>';
        echo '<div>Reduction: <span>' . $reduction . '%</span></div>';
        echo '</div>';
        echo '<div class="code-section"><span class="code-label">Function</span>';
        echo '<pre class="fn-call">$min->call("' . $type . '", file_get_contents("' . $type . ' input"));</pre>';
        echo '</div>';
        echo '<div class="code-section"><span class="code-label">Output</span>';
        $display = mb_substr($result, 0, $maxChars);
        if (mb_strlen($result) > $maxChars) $display .= '...';
        echo '<pre>' . htmlspecialchars($display) . '</pre>';
        echo '</div>';
        echo '</div>';
    }

    showMinify($min, 'css', 'CSS Minification', file_get_contents(__DIR__ . '/styles.css'), $maxChars);
    showMinify($min, 'js', 'JavaScript Minification', file_get_contents(__DIR__ . '/script.js'), $maxChars);
    showMinify($min, 'html', 'HTML Minification', file_get_contents(__DIR__ . '/page.html'), $maxChars);

    $svgInput = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
    <circle cx="50" cy="50" r="40" fill="#3498db" stroke="#2980b9" stroke-width="3" />
    <text x="50" y="55" text-anchor="middle" fill="white" font-size="20" font-family="Arial">
        SVG
    </text>
</svg>';
    showMinify($min, 'svg', 'SVG Minification', $svgInput, $maxChars);
    ?>

<?php include __DIR__ . '/../_footer.php'; ?>
