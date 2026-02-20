<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'QR Code Generator';
$_prev = ['url' => '../html-rewriter/', 'label' => 'HTML Rewriter'];
$_next = ['url' => '../toml/', 'label' => 'TOML'];
$_styleExtra = <<<'CSS'
        .example-output svg { max-width: 100%; height: auto; }
CSS;
include __DIR__ . '/../_header.php';
?>

    <div class="intro">
        <div class="intro-badges">
            <span class="badge badge-go">Go</span>
            <?php if (isset($_sizes['qrcode'])): ?><span class="badge badge-size"><?= _fmtSize($_sizes['qrcode']) ?></span><?php endif; ?>
        </div>
        <h1>QR Code Generator</h1>
        <p>Generate QR codes as SVG from text input using a WebAssembly plugin.</p>
        <p><code>$qr->call('generate', ['text' => ..., 'size' => ...])</code></p>
    </div>

    <?php $qr = new Wasm('qrcode'); ?>

    <?php
    // Example 1: Simple URL
    $text1 = 'https://github.com/example';
    $start = microtime(true);
    $result1 = $qr->call('generate', ['text' => $text1, 'size' => 200]);
    $time1 = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #10B981">
        <div class="example-header">
            <h2>URL</h2>
            <span class="timing"><?= number_format($time1, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output" style="display: flex; align-items: center; justify-content: center;">
                <?= $result1 ?>
            </div>
            <div class="example-code">
                <div class="label">Input</div>
                <pre><?= htmlspecialchars($text1) ?></pre>
            </div>
        </div>
    </div>

    <?php
    // Example 2: WiFi config
    $text2 = 'WIFI:T:WPA;S:MyNetwork;P:MyPassword;;';
    $start = microtime(true);
    $result2 = $qr->call('generate', ['text' => $text2, 'size' => 200]);
    $time2 = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #10B981">
        <div class="example-header">
            <h2>WiFi Configuration</h2>
            <span class="timing"><?= number_format($time2, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output" style="display: flex; align-items: center; justify-content: center;">
                <?= $result2 ?>
            </div>
            <div class="example-code">
                <div class="label">Input</div>
                <pre><?= htmlspecialchars($text2) ?></pre>
            </div>
        </div>
    </div>

    <?php
    // Example 3: Plain text
    $text3 = 'Hello from FrankenWASM!';
    $start = microtime(true);
    $result3 = $qr->call('generate', ['text' => $text3, 'size' => 150]);
    $time3 = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #10B981">
        <div class="example-header">
            <h2>Plain Text</h2>
            <span class="timing"><?= number_format($time3, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output" style="display: flex; align-items: center; justify-content: center;">
                <?= $result3 ?>
            </div>
            <div class="example-code">
                <div class="label">Input</div>
                <pre><?= htmlspecialchars($text3) ?></pre>
            </div>
        </div>
    </div>

    <?php
    // Example 4: vCard
    $text4 = "BEGIN:VCARD\nVERSION:3.0\nN:Doe;John\nTEL:+1234567890\nEND:VCARD";
    $start = microtime(true);
    $result4 = $qr->call('generate', ['text' => $text4, 'size' => 200]);
    $time4 = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #10B981">
        <div class="example-header">
            <h2>vCard Contact</h2>
            <span class="timing"><?= number_format($time4, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output" style="display: flex; align-items: center; justify-content: center;">
                <?= $result4 ?>
            </div>
            <div class="example-code">
                <div class="label">Input</div>
                <pre><?= htmlspecialchars($text4) ?></pre>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../_footer.php'; ?>
