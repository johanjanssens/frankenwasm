<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'Diagrams';
$_prev = ['url' => '../katex/', 'label' => 'KaTeX'];
$_next = ['url' => '../sanitize/', 'label' => 'Sanitize'];
$_styleExtra = <<<'CSS'
        .svg-wrap { max-width: 100%; height: auto; overflow: hidden; }
        .svg-wrap svg { max-width: 100%; height: auto; }
CSS;
include __DIR__ . '/../_header.php';
?>

    <div class="intro">
        <div class="intro-badges">
            <span class="badge badge-js">JS</span>
            <?php if (isset($_sizes['nomnoml'])): ?><span class="badge badge-size"><?= _fmtSize($_sizes['nomnoml']) ?></span><?php endif; ?>
        </div>
        <h1>Diagram Rendering</h1>
        <p>Render UML diagrams and flowcharts from text notation using <a href="https://nomnoml.com" target="_blank">nomnoml</a>, compiled to WebAssembly.</p>
        <p><code>$diagram->call('render', ['code' => ...])</code></p>
    </div>

    <?php $diagram = new Wasm('nomnoml'); ?>

    <?php
    $code1 = '[User|name: string;email: string|login();logout()] -> [Session|token: string;expires: date|validate()]
[User] -> [Order|items: array;total: float|calculate()]
[Order] -> [Product|name: string;price: float]';
    $start = microtime(true);
    $result1 = $diagram->call('render', ['code' => $code1]);
    $time1 = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #F7DF1E">
        <div class="example-header">
            <h2>Class Diagram</h2>
            <span class="timing"><?= number_format($time1, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output">
                <div class="label">Rendered Output</div>
                <div class="svg-wrap"><?= $result1 ?></div>
            </div>
            <div class="example-code">
                <div class="label">Nomnoml Source</div>
                <pre><?= htmlspecialchars($code1) ?></pre>
            </div>
        </div>
    </div>

    <?php
    $code2 = '[Request] -> [Router]
[Router] -> [Controller]
[Controller] -> [Model]
[Model] -> [Database]
[Controller] -> [View]
[View] -> [Response]';
    $start = microtime(true);
    $result2 = $diagram->call('render', ['code' => $code2]);
    $time2 = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #F7DF1E">
        <div class="example-header">
            <h2>Flow Diagram</h2>
            <span class="timing"><?= number_format($time2, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output">
                <div class="label">Rendered Output</div>
                <div class="svg-wrap"><?= $result2 ?></div>
            </div>
            <div class="example-code">
                <div class="label">Nomnoml Source</div>
                <pre><?= htmlspecialchars($code2) ?></pre>
            </div>
        </div>
    </div>

    <?php
    $code3 = '[<frame> FrankenWASM|
  [PHP Request] -> [Extism Host]
  [Extism Host] -> [WASM Plugin]
  [WASM Plugin] -> [Result]
  [Result] -> [PHP Response]
]';
    $start = microtime(true);
    $result3 = $diagram->call('render', ['code' => $code3]);
    $time3 = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #F7DF1E">
        <div class="example-header">
            <h2>Architecture</h2>
            <span class="timing"><?= number_format($time3, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output">
                <div class="label">Rendered Output</div>
                <div class="svg-wrap"><?= $result3 ?></div>
            </div>
            <div class="example-code">
                <div class="label">Nomnoml Source</div>
                <pre><?= htmlspecialchars($code3) ?></pre>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../_footer.php'; ?>
