<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'JSONPath';
$_prev = ['url' => '../highlight/', 'label' => 'Highlighting'];
$_next = ['url' => '../minify/', 'label' => 'Minify'];
$_styleExtra = <<<'CSS'
        .query {
            background: #f0f7ff;
            border-left: 4px solid #3B82F6;
            padding: 10px 15px;
            margin: 12px 0;
            font-family: monospace;
            font-size: 0.9rem;
        }
        html.dark .query { background: #1e2a45; border-left-color: #3B82F6; color: #cdd6f4; }
        html.dark .query a { color: #64b5f6; }
        .panel pre { max-height: 300px; overflow-y: auto; }
        .container { max-width: 900px; }
CSS;
include __DIR__ . '/../_header.php';
?>

    <div class="intro">
        <div class="intro-badges">
            <span class="badge badge-rust">Rust</span>
            <?php if (isset($_sizes['jsonpath-rs'])): ?><span class="badge badge-size"><?= _fmtSize($_sizes['jsonpath-rs']) ?></span><?php endif; ?>
        </div>
        <h1>JSONPath Queries</h1>
        <p>Query JSON data with <code>$jp->call('select', ['data' => ..., 'query' => ...])</code></p>
    </div>

    <?php $jp = new Wasm('jsonpath-rs'); ?>

    <div class="container">
        <div class="panel" style="border-top: 5px solid #3B82F6">
            <h2>Inline Data</h2>
            <?php
            $iterations = 10;
            $totalTime = 0;
            $result = '';

            for ($i = 0; $i < $iterations; $i++) {
                $start = microtime(true);
                $result = $jp->call('select', [
                    'data' => [
                        'store' => [
                            'book' => [
                                ['category' => 'reference', 'author' => 'Nigel Rees', 'title' => 'Sayings of the Century', 'price' => 8.95],
                                ['category' => 'fiction', 'author' => 'Evelyn Waugh', 'title' => 'Sword of Honour', 'price' => 12.99],
                                ['category' => 'fiction', 'author' => 'Herman Melville', 'title' => 'Moby Dick', 'price' => 8.99],
                                ['category' => 'fiction', 'author' => 'J.R.R. Tolkien', 'title' => 'The Lord of the Rings', 'price' => 22.99],
                            ],
                            'bicycle' => ['color' => 'red', 'price' => 19.95],
                        ],
                    ],
                    'query' => '$.store.book[?(@.price < 10)]',
                ]);
                $totalTime += microtime(true) - $start;
            }
            $avgTime = ($totalTime / $iterations) * 1000;
            ?>
            <div class="timing-info">Average (<?= $iterations ?> runs): <?= number_format($avgTime, 4) ?> ms</div>
            <div class="query">Query: <code>$.store.book[?(@.price &lt; 10)]</code></div>
            <pre><?= htmlspecialchars(is_string($result) ? $result : json_encode($result, JSON_PRETTY_PRINT)) ?></pre>
        </div>

        <div class="panel" style="border-top: 5px solid #10B981">
            <h2>HTTP API</h2>
            <?php
            $start = microtime(true);
            $result = $jp->call('select', [
                'url' => 'https://jsonplaceholder.typicode.com/users',
                'query' => '$[?(@.email)].name',
            ]);
            $time = (microtime(true) - $start) * 1000;
            ?>
            <div class="timing-info">Execution: <?= number_format($time, 2) ?> ms</div>
            <div class="query">Source: <a href="https://jsonplaceholder.typicode.com/users" target="_blank">jsonplaceholder.typicode.com/users</a></div>
            <div class="query">Query: <code>$[?(@.email)].name</code></div>
            <pre><?= htmlspecialchars(is_string($result) ? $result : json_encode($result, JSON_PRETTY_PRINT)) ?></pre>
        </div>
    </div>

<?php include __DIR__ . '/../_footer.php'; ?>
