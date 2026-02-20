<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'HTML Rewriter';
$_prev = ['url' => '../jsx-include/', 'label' => 'JSX / React'];
$_next = ['url' => '../qrcode/', 'label' => 'QR Code'];
$_styleExtra = <<<'CSS'
        .example-left {
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .example-left .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .example-left pre {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 12.5px;
            border: 1px solid #eee;
            white-space: pre-wrap;
        }
        .example-left .output-pre { background: #f0f7ff; border-color: #bfdbfe; }
        html.dark .example-left pre { background: #0d1117; border-color: #21262d; color: #cdd6f4; }
        html.dark .example-left .output-pre { background: #0d1a2e; border-color: #1e3a5c; }
CSS;
include __DIR__ . '/../_header.php';
?>

    <div class="intro">
        <div class="intro-badges">
            <span class="badge badge-js">JS</span>
            <?php if (isset($_sizes['html-rewriter'])): ?><span class="badge badge-size"><?= _fmtSize($_sizes['html-rewriter']) ?></span><?php endif; ?>
        </div>
        <h1>HTML Rewriter</h1>
        <p>Transform HTML with inline JavaScript functions via <code>$rw->call('transform', [...])</code></p>
    </div>

    <?php $rw = new Wasm('html-rewriter'); ?>

    <?php
    // Example 1: Class swap + text change
    $html1 = '<div class="greeter"><p>Hello...</p></div>';
    $transformer1 = "function Transformer(\$, props) {\n    \$('.greeter')\n        .removeClass('greeter')\n        .addClass(props.class)\n        .find('p').text(props.message);\n}";
    $input1 = [
        'html' => $html1,
        'transformations' => [$transformer1],
        'props' => ['class' => 'greeted', 'message' => 'Hello world from FrankenWASM!'],
    ];

    $start = microtime(true);
    $result1 = $rw->call('transform', $input1);
    $time1 = (microtime(true) - $start) * 1000;
    ?>
    <div class="example" style="border-top: 4px solid #F7DF1E">
        <div class="example-header">
            <h2>Class Swap + Text Replace</h2>
            <span class="timing"><?= number_format($time1, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-left">
                <div>
                    <div class="label">Input HTML</div>
                    <pre><?= htmlspecialchars($html1) ?></pre>
                </div>
                <div>
                    <div class="label">Output HTML</div>
                    <pre class="output-pre"><?= htmlspecialchars(is_string($result1) ? $result1 : json_encode($result1)) ?></pre>
                </div>
            </div>
            <div class="example-code">
                <div class="label">Transformer Function</div>
                <pre><?= htmlspecialchars($transformer1) ?></pre>
            </div>
        </div>
    </div>

    <?php
    // Example 2: Add attributes + wrap content
    $html2 = '<ul><li>Alpha</li><li>Beta</li><li>Gamma</li></ul>';
    $transformer2 = "function Transformer(\$, props) {\n    \$('li').each(function(i) {\n        \$(this).attr('data-index', i);\n        if (props.highlight &&\n            props.highlight.includes(\$(this).text())) {\n            \$(this).addClass('highlight');\n        }\n    });\n}";
    $input2 = [
        'html' => $html2,
        'transformations' => [$transformer2],
        'props' => ['highlight' => ['Beta']],
    ];

    $start = microtime(true);
    $result2 = $rw->call('transform', $input2);
    $time2 = (microtime(true) - $start) * 1000;
    ?>
    <div class="example" style="border-top: 4px solid #F7DF1E">
        <div class="example-header">
            <h2>Add Attributes + Conditional Class</h2>
            <span class="timing"><?= number_format($time2, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-left">
                <div>
                    <div class="label">Input HTML</div>
                    <pre><?= htmlspecialchars($html2) ?></pre>
                </div>
                <div>
                    <div class="label">Output HTML</div>
                    <pre class="output-pre"><?= htmlspecialchars(is_string($result2) ? $result2 : json_encode($result2)) ?></pre>
                </div>
            </div>
            <div class="example-code">
                <div class="label">Transformer Function</div>
                <pre><?= htmlspecialchars($transformer2) ?></pre>
            </div>
        </div>
    </div>

    <?php
    // Example 3: Multiple transformations chained
    $html3 = '<div><h1>Title</h1><p>Some content here.</p><p>More content.</p></div>';
    $t3a = "function Transformer(\$, props) {\n    \$('h1').text(props.title);\n}";
    $t3b = "function Transformer(\$, props) {\n    \$('p').wrap('<blockquote></blockquote>');\n}";
    $input3 = [
        'html' => $html3,
        'transformations' => [$t3a, $t3b],
        'props' => ['title' => 'Transformed Title'],
    ];

    $start = microtime(true);
    $result3 = $rw->call('transform', $input3);
    $time3 = (microtime(true) - $start) * 1000;
    ?>
    <div class="example" style="border-top: 4px solid #F7DF1E">
        <div class="example-header">
            <h2>Chained Transformations</h2>
            <span class="timing"><?= number_format($time3, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-left">
                <div>
                    <div class="label">Input HTML</div>
                    <pre><?= htmlspecialchars($html3) ?></pre>
                </div>
                <div>
                    <div class="label">Output HTML</div>
                    <pre class="output-pre"><?= htmlspecialchars(is_string($result3) ? $result3 : json_encode($result3)) ?></pre>
                </div>
            </div>
            <div class="example-code">
                <div class="label">Transformer Functions</div>
                <pre><?= htmlspecialchars("// Transform 1: Update title\n" . $t3a . "\n\n// Transform 2: Wrap paragraphs\n" . $t3b) ?></pre>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../_footer.php'; ?>
