<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'SCSS Compiler';
$_prev = ['url' => '../sanitize/', 'label' => 'Sanitize'];
$_next = ['url' => '../blurhash/', 'label' => 'Blurhash'];
$_styleExtra = <<<'CSS'
        .query {
            background: #fce4ec;
            border-left: 4px solid #CD6799;
            padding: 10px 15px;
            margin: 12px 0;
            font-family: monospace;
            font-size: 0.9rem;
        }
        html.dark .query { background: #2a1a2e; border-left-color: #CD6799; color: #cdd6f4; }
        .panel pre { max-height: 300px; overflow-y: auto; }
        .container { max-width: 900px; }
CSS;
include __DIR__ . '/../_header.php';
?>

    <div class="intro">
        <div class="intro-badges">
            <span class="badge badge-rust">Rust</span>
            <?php if (isset($_sizes['scss-rs'])): ?><span class="badge badge-size"><?= _fmtSize($_sizes['scss-rs']) ?></span><?php endif; ?>
        </div>
        <h1>SCSS Compiler</h1>
        <p>Compile SCSS/Sass to CSS using <code>$scss->call('compile', $input)</code> — powered by the <code>grass</code> crate</p>
    </div>

    <?php $scss = new Wasm('scss-rs'); ?>

    <div class="container">
        <div class="panel" style="border-top: 5px solid #CD6799">
            <h2>Variables &amp; Nesting</h2>
            <?php
            $scssInput = <<<'SCSS'
$primary: #3498db;
$secondary: #2ecc71;
$font-stack: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
$radius: 8px;

body {
    font-family: $font-stack;
    color: #333;

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    border-radius: $radius;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.3s ease;

    &-primary {
        background: $primary;
        color: white;

        &:hover {
            background: darken($primary, 10%);
        }
    }

    &-secondary {
        background: $secondary;
        color: white;

        &:hover {
            background: darken($secondary, 10%);
        }
    }
}
SCSS;

            $iterations = 10;
            $totalTime = 0;
            $result = '';

            for ($i = 0; $i < $iterations; $i++) {
                $start = microtime(true);
                $result = $scss->call('compile', $scssInput);
                $totalTime += microtime(true) - $start;
            }
            $avgTime = ($totalTime / $iterations) * 1000;
            ?>
            <div class="timing-info">Average (<?= $iterations ?> runs): <?= number_format($avgTime, 4) ?> ms</div>
            <div class="query">Input (SCSS):</div>
            <pre><?= htmlspecialchars($scssInput) ?></pre>
            <div class="query" style="margin-top: 16px;">Output (CSS):</div>
            <pre><?= htmlspecialchars(is_string($result) ? $result : json_encode($result, JSON_PRETTY_PRINT)) ?></pre>
        </div>

        <div class="panel" style="border-top: 5px solid #CD6799">
            <h2>Mixins &amp; Functions</h2>
            <?php
            $scssInput2 = <<<'SCSS'
@mixin flex-center($direction: row) {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: $direction;
}

@mixin responsive($breakpoint) {
    @if $breakpoint == mobile {
        @media (max-width: 768px) { @content; }
    } @else if $breakpoint == tablet {
        @media (max-width: 1024px) { @content; }
    }
}

.hero {
    @include flex-center(column);
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

    h1 {
        font-size: 3rem;
        color: white;
    }

    @include responsive(mobile) {
        min-height: 60vh;

        h1 { font-size: 1.8rem; }
    }
}

.card-grid {
    @include flex-center;
    flex-wrap: wrap;
    gap: 24px;
}
SCSS;

            $start = microtime(true);
            $result2 = $scss->call('compile', $scssInput2);
            $time = (microtime(true) - $start) * 1000;
            ?>
            <div class="timing-info">Execution: <?= number_format($time, 4) ?> ms</div>
            <div class="query">Input (SCSS):</div>
            <pre><?= htmlspecialchars($scssInput2) ?></pre>
            <div class="query" style="margin-top: 16px;">Output (CSS):</div>
            <pre><?= htmlspecialchars(is_string($result2) ? $result2 : json_encode($result2, JSON_PRETTY_PRINT)) ?></pre>
        </div>
    </div>

<?php include __DIR__ . '/../_footer.php'; ?>
