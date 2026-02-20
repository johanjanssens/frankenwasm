<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'Enhance SSR';
$_prev = ['url' => '../minify/', 'label' => 'Minify'];
$_next = ['url' => '../jsx-include/', 'label' => 'JSX / React'];
$_styleExtra = <<<'CSS'
        .concept {
            max-width: 900px;
            margin: 0 auto 24px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .concept-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            text-align: center;
        }
        .concept-card .icon { font-size: 1.8rem; margin-bottom: 8px; }
        .concept-card h3 { font-size: 0.95rem; color: #2c3e50; margin-bottom: 6px; }
        .concept-card p { font-size: 0.82rem; color: #666; }
        html.dark .concept-card { background: #16213e; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        html.dark .concept-card h3 { color: #e0e0e0; }
        html.dark .concept-card p { color: #aaa; }
        @media (max-width: 768px) { .concept { grid-template-columns: 1fr; } }
CSS;
include __DIR__ . '/../_header.php';
?>

    <div class="intro">
        <div class="intro-badges">
            <span class="badge badge-js">JS</span>
            <?php if (isset($_sizes['enhance-ssr'])): ?><span class="badge badge-size"><?= _fmtSize($_sizes['enhance-ssr']) ?></span><?php endif; ?>
        </div>
        <h1>Enhance SSR</h1>
        <p>Server-side render custom HTML elements using <a href="https://enhance.dev/docs/enhance-ssr" target="_blank">@enhance/ssr</a>, compiled to WebAssembly.</p>
        <p><code>$ssr->call('render', ['html' => ..., 'elements' => ..., 'state' => ...])</code></p>
    </div>

    <div class="concept">
        <div class="concept-card">
            <div class="icon">&#x1F3F7;</div>
            <h3>Custom Elements</h3>
            <p>Define components as tagged template functions that return HTML + scoped styles</p>
        </div>
        <div class="concept-card">
            <div class="icon">&#x1F4E6;</div>
            <h3>Slot Projection</h3>
            <p>Use <code>&lt;slot&gt;</code> to project content from the host element into the component</p>
        </div>
        <div class="concept-card">
            <div class="icon">&#x1F4CA;</div>
            <h3>Shared State</h3>
            <p>Pass a state store that all elements can read from during rendering</p>
        </div>
    </div>

    <?php $ssr = new Wasm('enhance-ssr'); ?>

    <?php
    $input1 = [
        'html' => '<my-header>Hello from Enhance SSR!</my-header>',
        'elements' => [
            'my-header' => 'function({ html }) { return html`<style>h1 { color: #2c3e50; font-size: 1.8rem; text-align: center; padding: 1rem 0; border-bottom: 2px solid #3498db; }</style><h1><slot></slot></h1>` }',
        ],
        'state' => [],
    ];
    $start = microtime(true);
    $result1 = $ssr->call('render', $input1);
    $time1 = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #10B981">
        <div class="example-header">
            <h2>Custom Header Element</h2>
            <span class="timing"><?= number_format($time1, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output">
                <div class="label">Rendered Output</div>
                <?= $result1 ?>
            </div>
            <div class="example-code">
                <div class="label">PHP Code</div>
                <pre><?= htmlspecialchars(<<<'CODE'
$input = [
  'html' => '<my-header>Hello!</my-header>',
  'elements' => [
    'my-header' => 'function({ html }) {
      return html`
        <style>
          h1 { color: #2c3e50;
               border-bottom: 2px solid #3498db; }
        </style>
        <h1><slot></slot></h1>`
    }',
  ],
];

$result = $ssr->call('render', $input);
CODE) ?></pre>
            </div>
        </div>
    </div>

    <?php
    $input2 = [
        'html' => '<my-card></my-card>',
        'elements' => [
            'my-card' => 'function({ html, state }) { return html`<style>.card { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 400px; margin: 0 auto; } .card h2 { color: #2c3e50; margin-bottom: 8px; } .card p { color: #666; }</style><div class="card"><h2>${state.store?.title || "Card Title"}</h2><p>${state.store?.description || "Card description"}</p></div>` }',
        ],
        'state' => [
            'store' => [
                'title' => 'WebAssembly Plugins',
                'description' => 'Build server-side rendered components using custom HTML elements powered by WASM.',
            ],
        ],
    ];
    $start = microtime(true);
    $result2 = $ssr->call('render', $input2);
    $time2 = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #10B981">
        <div class="example-header">
            <h2>Card with State</h2>
            <span class="timing"><?= number_format($time2, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output">
                <div class="label">Rendered Output</div>
                <?= $result2 ?>
            </div>
            <div class="example-code">
                <div class="label">PHP Code</div>
                <pre><?= htmlspecialchars(<<<'CODE'
$input = [
  'html' => '<my-card></my-card>',
  'elements' => [
    'my-card' => 'function({ html, state }) {
      return html`
        <style>
          .card { border-radius: 8px; padding: 20px;
                  box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        </style>
        <div class="card">
          <h2>${state.store?.title}</h2>
          <p>${state.store?.description}</p>
        </div>`
    }',
  ],
  'state' => [
    'store' => [
      'title' => 'WebAssembly Plugins',
      'description' => 'Build SSR components.',
    ],
  ],
];
CODE) ?></pre>
            </div>
        </div>
    </div>

    <?php
    $input3 = [
        'html' => '<my-footer>Built with FrankenWASM</my-footer>',
        'elements' => [
            'my-footer' => 'function({ html }) { return html`<style>footer { background: #f5f5f5; padding: 16px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 0.9rem; } .copyright { margin-top: 8px; font-weight: bold; }</style><footer><div class="text"><slot></slot></div><div class="copyright">&copy; 2025</div></footer>` }',
        ],
        'state' => [],
    ];
    $start = microtime(true);
    $result3 = $ssr->call('render', $input3);
    $time3 = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #10B981">
        <div class="example-header">
            <h2>Footer with Slots</h2>
            <span class="timing"><?= number_format($time3, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output">
                <div class="label">Rendered Output</div>
                <?= $result3 ?>
            </div>
            <div class="example-code">
                <div class="label">PHP Code</div>
                <pre><?= htmlspecialchars(<<<'CODE'
$input = [
  'html' => '<my-footer>Built with FrankenWASM</my-footer>',
  'elements' => [
    'my-footer' => 'function({ html }) {
      return html`
        <style>
          footer { background: #f5f5f5; padding: 16px;
                   text-align: center; }
        </style>
        <footer>
          <div><slot></slot></div>
          <div class="copyright">&copy; 2025</div>
        </footer>`
    }',
  ],
];
CODE) ?></pre>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../_footer.php'; ?>
