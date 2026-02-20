<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'HTML Sanitize';
$_prev = ['url' => '../diagrams/', 'label' => 'Diagrams'];
$_next = ['url' => '../scss/', 'label' => 'SCSS'];
$_styleExtra = <<<'CSS'
        .input-section {
            max-width: 95%;
            margin: 0 auto 24px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .input-section h2 { font-size: 1.2rem; color: #2c3e50; margin-bottom: 12px; text-align: center; }
        .input-section pre { white-space: pre-wrap; word-wrap: break-word; }
        html.dark .input-section { background: #16213e; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        html.dark .input-section h2 { color: #e0e0e0; }
CSS;
include __DIR__ . '/../_header.php';
?>

    <div class="intro intro-wide">
        <div class="intro-badges">
            <span class="badge badge-go">Go</span>
            <span class="badge badge-rust">Rust</span>
            <span class="badge badge-js">JS</span>
            <?php $sanTotal = ($_sizes['sanitize'] ?? 0) + ($_sizes['sanitize-rs'] ?? 0) + ($_sizes['sanitize-js'] ?? 0); ?>
            <?php if ($sanTotal): ?><span class="badge badge-size"><?= _fmtSize($sanTotal) ?></span><?php endif; ?>
        </div>
        <h1>HTML Sanitization</h1>
        <p>Compare <code>sanitize(html)</code> across Go (<a href="https://github.com/microcosm-cc/bluemonday">bluemonday</a>), Rust (<a href="https://github.com/rust-ammonia/ammonia">ammonia</a>), and JavaScript (<a href="https://github.com/apostrophecms/sanitize-html">sanitize-html</a>) WASM plugins</p>
    </div>

    <?php
    $dangerousHtml = '<h1>Welcome</h1>
<p>This is <b>safe</b> content with a <a href="https://example.com">link</a>.</p>
<script>alert("XSS attack!")</script>
<img src="photo.jpg" alt="Photo" onerror="alert(\'xss\')">
<p style="color: red" onclick="steal()">Styled text</p>
<iframe src="https://evil.com"></iframe>
<div onmouseover="hack()">Hover me</div>
<a href="javascript:alert(1)">Click me</a>
<math><mi>x</mi></math>';
    ?>

    <div class="input-section">
        <h2>Dangerous HTML Input</h2>
        <pre><?= htmlspecialchars($dangerousHtml) ?></pre>
    </div>

    <div class="container">
        <div class="pane" style="border-top: 5px solid #00ADD8">
            <h2>Go</h2>
            <a href="https://github.com/microcosm-cc/bluemonday" target="_blank" class="lib-link">microcosm-cc/bluemonday</a>
            <?php
            $goSan = new Wasm('sanitize');
            $start = microtime(true);
            $result = $goSan->call('sanitize', $dangerousHtml);
            $time = (microtime(true) - $start) * 1000;
            ?>
            <div class="timing-info">Sanitize: <?= number_format($time, 2) ?> ms</div>
            <pre><?= htmlspecialchars($result) ?></pre>
        </div>

        <div class="pane" style="border-top: 5px solid #DEA584">
            <h2>Rust</h2>
            <a href="https://github.com/rust-ammonia/ammonia" target="_blank" class="lib-link">rust-ammonia/ammonia</a>
            <?php
            $rsSan = new Wasm('sanitize-rs');
            $start = microtime(true);
            $result = $rsSan->call('sanitize', $dangerousHtml);
            $time = (microtime(true) - $start) * 1000;
            ?>
            <div class="timing-info">Sanitize: <?= number_format($time, 2) ?> ms</div>
            <pre><?= htmlspecialchars($result) ?></pre>
        </div>

        <div class="pane" style="border-top: 5px solid #F7DF1E">
            <h2>JavaScript</h2>
            <a href="https://github.com/apostrophecms/sanitize-html" target="_blank" class="lib-link">apostrophecms/sanitize-html</a>
            <?php
            $jsSan = new Wasm('sanitize-js');
            $start = microtime(true);
            $result = $jsSan->call('sanitize', $dangerousHtml);
            $time = (microtime(true) - $start) * 1000;
            ?>
            <div class="timing-info">Sanitize: <?= number_format($time, 2) ?> ms</div>
            <pre><?= htmlspecialchars($result) ?></pre>
        </div>
    </div>

<?php include __DIR__ . '/../_footer.php'; ?>
