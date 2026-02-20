<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'Markdown';
$_prev = ['url' => '../langdetect/', 'label' => 'Language Detect'];
$_next = ['url' => '../highlight/', 'label' => 'Highlighting'];
include __DIR__ . '/../_header.php';
?>

    <div class="intro intro-wide">
        <div class="intro-badges">
            <span class="badge badge-go">Go</span>
            <span class="badge badge-rust">Rust</span>
            <span class="badge badge-js">JS</span>
            <?php $mdTotal = ($_sizes['markdown'] ?? 0) + ($_sizes['markdown-rs'] ?? 0) + ($_sizes['markdown-js'] ?? 0); ?>
            <?php if ($mdTotal): ?><span class="badge badge-size"><?= _fmtSize($mdTotal) ?></span><?php endif; ?>
        </div>
        <h1>Markdown Rendering</h1>
        <p>Compare <code>convert(markdown)</code> across Go, Rust, and JavaScript WASM plugins</p>
    </div>

    <?php $markdown = file_get_contents(__DIR__ . '/markdown.md'); ?>

    <div class="container">
        <div class="pane" style="border-top: 5px solid #00ADD8">
            <h2>Go</h2>
            <a href="https://github.com/gomarkdown/markdown" target="_blank" class="lib-link">gomarkdown/markdown</a>
            <?php
            $md = new Wasm('markdown');
            $start = microtime(true);
            $result = $md->call('convert', $markdown);
            $time = (microtime(true) - $start) * 1000;
            ?>
            <div class="timing-info">Conversion: <?= number_format($time, 2) ?> ms</div>
            <?= $result ?>
        </div>

        <div class="pane" style="border-top: 5px solid #DEA584">
            <h2>Rust</h2>
            <a href="https://github.com/wooorm/markdown-rs" target="_blank" class="lib-link">wooorm/markdown-rs</a>
            <?php
            $md = new Wasm('markdown-rs');
            $start = microtime(true);
            $result = $md->call('convert', $markdown);
            $time = (microtime(true) - $start) * 1000;
            ?>
            <div class="timing-info">Conversion: <?= number_format($time, 2) ?> ms</div>
            <?= $result ?>
        </div>

        <div class="pane" style="border-top: 5px solid #F7DF1E">
            <h2>JavaScript</h2>
            <a href="https://github.com/markdown-it/markdown-it" target="_blank" class="lib-link">markdown-it</a>
            <?php
            $md = new Wasm('markdown-js');
            $start = microtime(true);
            $result = $md->call('convert', $markdown);
            $time = (microtime(true) - $start) * 1000;
            ?>
            <div class="timing-info">Conversion: <?= number_format($time, 2) ?> ms</div>
            <?= $result ?>
        </div>
    </div>

<?php include __DIR__ . '/../_footer.php'; ?>
