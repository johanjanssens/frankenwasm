<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'TOML';
$_prev = ['url' => '../qrcode/', 'label' => 'QR Code'];
$_next = ['url' => '../katex/', 'label' => 'KaTeX'];
$_styleExtra = <<<'CSS'
        .query {
            background: #fdf6f0;
            border-left: 4px solid #DEA584;
            padding: 10px 15px;
            margin: 12px 0;
            font-family: monospace;
            font-size: 0.9rem;
        }
        html.dark .query { background: #1e2a45; border-left-color: #DEA584; color: #cdd6f4; }
        html.dark .query a { color: #64b5f6; }
        .panel pre { max-height: 300px; overflow-y: auto; }
        .container { max-width: 900px; }
CSS;
include __DIR__ . '/../_header.php';
?>

    <div class="intro">
        <div class="intro-badges">
            <span class="badge badge-rust">Rust</span>
            <?php if (isset($_sizes['toml-rs'])): ?><span class="badge badge-size"><?= _fmtSize($_sizes['toml-rs']) ?></span><?php endif; ?>
        </div>
        <h1>TOML Parser</h1>
        <p>Convert between TOML and JSON using <code>$toml->call('parse', ...)</code> and <code>$toml->call('serialize', ...)</code></p>
    </div>

    <?php $toml = new Wasm('toml-rs'); ?>

    <div class="container">
        <div class="panel" style="border-top: 5px solid #DEA584">
            <h2>TOML &rarr; JSON</h2>
            <?php
            $tomlInput = <<<'TOML'
[package]
name = "frankenwasm"
version = "0.1.0"
edition = "2021"

[dependencies]
extism-pdk = "1.3.0"
serde_json = "1"

[server]
host = "0.0.0.0"
port = 8080
debug = true
workers = 4

[[plugins]]
name = "markdown"
lang = "go"

[[plugins]]
name = "katex"
lang = "js"
TOML;

            $iterations = 10;
            $totalTime = 0;
            $result = '';

            for ($i = 0; $i < $iterations; $i++) {
                $start = microtime(true);
                $result = $toml->call('parse', $tomlInput);
                $totalTime += microtime(true) - $start;
            }
            $avgTime = ($totalTime / $iterations) * 1000;
            ?>
            <div class="timing-info">Average (<?= $iterations ?> runs): <?= number_format($avgTime, 4) ?> ms</div>
            <div class="query">Input (TOML):</div>
            <pre><?= htmlspecialchars($tomlInput) ?></pre>
            <div class="query" style="margin-top: 16px;">Output (JSON):</div>
            <pre><?= htmlspecialchars(is_string($result) ? $result : json_encode($result, JSON_PRETTY_PRINT)) ?></pre>
        </div>

        <div class="panel" style="border-top: 5px solid #DEA584">
            <h2>JSON &rarr; TOML</h2>
            <?php
            $jsonInput = '{"database": {"host": "localhost", "port": 5432, "name": "myapp", "ssl": true}, "cache": {"driver": "redis", "ttl": 3600}}';

            $iterations = 10;
            $totalTime = 0;
            $result = '';

            for ($i = 0; $i < $iterations; $i++) {
                $start = microtime(true);
                $result = $toml->call('serialize', $jsonInput);
                $totalTime += microtime(true) - $start;
            }
            $avgTime = ($totalTime / $iterations) * 1000;
            ?>
            <div class="timing-info">Average (<?= $iterations ?> runs): <?= number_format($avgTime, 4) ?> ms</div>
            <div class="query">Input (JSON):</div>
            <pre><?= htmlspecialchars(json_encode(json_decode($jsonInput), JSON_PRETTY_PRINT)) ?></pre>
            <div class="query" style="margin-top: 16px;">Output (TOML):</div>
            <pre><?= htmlspecialchars(is_string($result) ? $result : json_encode($result, JSON_PRETTY_PRINT)) ?></pre>
        </div>
    </div>

<?php include __DIR__ . '/../_footer.php'; ?>
