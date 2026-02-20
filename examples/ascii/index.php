<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'ASCII Art';
$_prev = ['url' => '../blurhash/', 'label' => 'Blurhash'];
$_next = ['url' => '../langdetect/', 'label' => 'Language Detect'];
$_styleExtra = <<<'CSS'
        .container { max-width: 900px; }
        .banner-block {
            background: #1a1a2e;
            color: #00ff41;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 16px 0;
        }
        .banner-block pre {
            background: transparent;
            color: inherit;
            padding: 0;
            margin: 0;
            border: none;
            font-size: 0.7rem;
            line-height: 1.15;
        }
        .font-label {
            display: inline-block;
            padding: 3px 10px;
            background: #e8f4fd;
            color: #1976d2;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        html.dark .font-label { background: #1e2a45; color: #64b5f6; }
CSS;
include __DIR__ . '/../_header.php';
?>

    <div class="intro">
        <div class="intro-badges">
            <span class="badge badge-rust">Rust</span>
            <?php if (isset($_sizes['ascii-rs'])): ?><span class="badge badge-size"><?= _fmtSize($_sizes['ascii-rs']) ?></span><?php endif; ?>
        </div>
        <h1>ASCII Art</h1>
        <p>Generate FIGlet-style text banners using <code>$art->call('banner', ['text' => ..., 'font' => ...])</code></p>
    </div>

    <?php $art = new Wasm('ascii-rs'); ?>

    <div class="container">
        <div class="panel" style="border-top: 5px solid #00ff41">
            <h2>Font Gallery</h2>
            <?php
            $texts = ['FrankenWASM', 'Hello PHP!', 'WASM'];
            $fonts = ['standard', 'big', 'slant', 'shadow', 'block'];

            foreach ($fonts as $font):
                $text = $texts[array_rand($texts)];
                $input = json_encode(['text' => $text, 'font' => $font]);
                $start = microtime(true);
                $result = $art->call('banner', $input);
                $time = (microtime(true) - $start) * 1000;
            ?>
                <span class="font-label"><?= $font ?></span>
                <div class="timing-info" style="display:inline; margin-left: 8px;"><?= number_format($time, 2) ?> ms</div>
                <div class="banner-block">
                    <pre><?= htmlspecialchars($result) ?></pre>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="panel" style="border-top: 5px solid #00ff41">
            <h2>All Fonts &times; One Word</h2>
            <?php
            $word = 'Rust';
            foreach ($fonts as $font):
                $input = json_encode(['text' => $word, 'font' => $font]);
                $start = microtime(true);
                $result = $art->call('banner', $input);
                $time = (microtime(true) - $start) * 1000;
            ?>
                <span class="font-label"><?= $font ?></span>
                <div class="banner-block">
                    <pre><?= htmlspecialchars($result) ?></pre>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

<?php include __DIR__ . '/../_footer.php'; ?>
