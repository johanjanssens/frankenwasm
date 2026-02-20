<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'Blurhash';
$_prev = ['url' => '../scss/', 'label' => 'SCSS'];
$_next = ['url' => '../ascii/', 'label' => 'ASCII Art'];
$_styleExtra = <<<'CSS'
        .container { max-width: 900px; }
        .demo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 24px;
            margin-top: 24px;
        }
        .demo-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .demo-card canvas {
            width: 100%;
            height: 150px;
            display: block;
        }
        .demo-card .info {
            padding: 16px;
        }
        .demo-card .hash {
            font-family: monospace;
            font-size: 0.8rem;
            background: #f0f0f0;
            padding: 6px 10px;
            border-radius: 4px;
            word-break: break-all;
            margin-top: 8px;
        }
        .demo-card .timing {
            font-size: 0.8rem;
            color: #888;
            margin-top: 6px;
        }
        html.dark .demo-card { background: #16213e; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        html.dark .demo-card .hash { background: #1e2a45; color: #cdd6f4; }
CSS;
include __DIR__ . '/../_header.php';
?>

    <div class="intro">
        <div class="intro-badges">
            <span class="badge badge-rust">Rust</span>
            <?php if (isset($_sizes['blurhash-rs'])): ?><span class="badge badge-size"><?= _fmtSize($_sizes['blurhash-rs']) ?></span><?php endif; ?>
        </div>
        <h1>Blurhash</h1>
        <p>Generate compact blur placeholders using <code>$bh->call('encode', ...)</code> and decode back with <code>$bh->call('decode', ...)</code></p>
    </div>

    <?php $bh = new Wasm('blurhash-rs'); ?>

    <div class="container">
        <div class="panel" style="border-top: 5px solid #8B5CF6">
            <h2>Encode &amp; Decode</h2>
            <p style="margin-bottom: 16px; color: #666;">Generate blurhash strings from raw pixel data, then decode them back to preview the blur placeholder.</p>

            <div class="demo-grid">
            <?php
            // Generate some test images as raw RGBA pixels
            $testImages = [
                ['name' => 'Sunset', 'w' => 32, 'h' => 32, 'fn' => function($x, $y, $w, $h) {
                    $r = (int)(255 * (1 - $y/$h) * 0.9 + 255 * 0.1);
                    $g = (int)(100 * (1 - $y/$h) + 50 * ($x/$w));
                    $b = (int)(50 + 150 * ($y/$h));
                    return [$r, $g, $b, 255];
                }],
                ['name' => 'Ocean', 'w' => 32, 'h' => 32, 'fn' => function($x, $y, $w, $h) {
                    $r = (int)(20 + 30 * sin($x * 0.3));
                    $g = (int)(80 + 60 * ($y/$h));
                    $b = (int)(150 + 100 * (1 - $y/$h));
                    return [$r, $g, $b, 255];
                }],
                ['name' => 'Forest', 'w' => 32, 'h' => 32, 'fn' => function($x, $y, $w, $h) {
                    $r = (int)(30 + 40 * sin($y * 0.2));
                    $g = (int)(100 + 80 * (1 - $y/$h) + 30 * sin($x * 0.3));
                    $b = (int)(20 + 30 * ($y/$h));
                    return [$r, $g, $b, 255];
                }],
                ['name' => 'Aurora', 'w' => 32, 'h' => 32, 'fn' => function($x, $y, $w, $h) {
                    $r = (int)(30 + 100 * sin($x * 0.15) * (1 - $y/$h));
                    $g = (int)(150 * (1 - $y/$h) + 50 * sin($x * 0.2));
                    $b = (int)(100 + 120 * ($y/$h));
                    return [max(0,min(255,$r)), max(0,min(255,$g)), max(0,min(255,$b)), 255];
                }],
            ];

            foreach ($testImages as $img):
                // Build raw RGBA pixel data
                $pixels = '';
                for ($y = 0; $y < $img['h']; $y++) {
                    for ($x = 0; $x < $img['w']; $x++) {
                        $rgba = ($img['fn'])($x, $y, $img['w'], $img['h']);
                        $pixels .= chr($rgba[0]) . chr($rgba[1]) . chr($rgba[2]) . chr($rgba[3]);
                    }
                }

                $start = microtime(true);
                $hash = $bh->call('encode', [
                    'pixels' => base64_encode($pixels),
                    'width' => $img['w'],
                    'height' => $img['h'],
                    'components_x' => 4,
                    'components_y' => 3,
                ]);
                $encodeTime = (microtime(true) - $start) * 1000;

                // Decode back to pixels for display
                $decodeInput = [
                    'hash' => is_string($hash) ? $hash : ($hash['hash'] ?? ''),
                    'width' => 64,
                    'height' => 48,
                ];

                $start = microtime(true);
                $decoded = $bh->call('decode', is_string($decodeInput) ? $decodeInput : $decodeInput);
                $decodeTime = (microtime(true) - $start) * 1000;
                $decodedData = is_array($decoded) ? $decoded : json_decode($decoded, true);
            ?>
                <div class="demo-card">
                    <canvas id="canvas-<?= strtolower($img['name']) ?>"
                            width="64" height="48"
                            data-pixels="<?= $decodedData['pixels'] ?? '' ?>"></canvas>
                    <div class="info">
                        <strong><?= $img['name'] ?></strong>
                        <div class="hash"><?= htmlspecialchars(is_string($hash) ? $hash : json_encode($hash)) ?></div>
                        <div class="timing">Encode: <?= number_format($encodeTime, 2) ?> ms | Decode: <?= number_format($decodeTime, 2) ?> ms</div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
    document.querySelectorAll('canvas[data-pixels]').forEach(canvas => {
        const b64 = canvas.dataset.pixels;
        if (!b64) return;
        const binary = atob(b64);
        const ctx = canvas.getContext('2d');
        const w = canvas.width, h = canvas.height;
        const imgData = ctx.createImageData(w, h);
        for (let i = 0; i < binary.length && i < imgData.data.length; i++) {
            imgData.data[i] = binary.charCodeAt(i);
        }
        ctx.putImageData(imgData, 0, 0);
    });
    </script>

<?php include __DIR__ . '/../_footer.php'; ?>
