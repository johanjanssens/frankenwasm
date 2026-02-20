<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'KaTeX';
$_prev = ['url' => '../toml/', 'label' => 'TOML'];
$_next = ['url' => '../diagrams/', 'label' => 'Diagrams'];
$_headExtra = '    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css">';
include __DIR__ . '/../_header.php';
?>

    <div class="intro">
        <div class="intro-badges">
            <span class="badge badge-js">JS</span>
            <?php if (isset($_sizes['katex'])): ?><span class="badge badge-size"><?= _fmtSize($_sizes['katex']) ?></span><?php endif; ?>
        </div>
        <h1>KaTeX Math Rendering</h1>
        <p>Render LaTeX math expressions to HTML on the server using <a href="https://katex.org/" target="_blank">KaTeX</a>, compiled to WebAssembly.</p>
        <p><code>$katex->call('render', ['expression' => ..., 'displayMode' => true])</code></p>
    </div>

    <?php $katex = new Wasm('katex'); ?>

    <?php
    $expr1 = 'x = \\frac{-b \\pm \\sqrt{b^2 - 4ac}}{2a}';
    $start = microtime(true);
    $result1 = $katex->call('render', ['expression' => $expr1, 'displayMode' => true]);
    $time1 = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #F7DF1E">
        <div class="example-header">
            <h2>Quadratic Formula</h2>
            <span class="timing"><?= number_format($time1, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output" style="display: flex; align-items: center; justify-content: center;">
                <?= $result1 ?>
            </div>
            <div class="example-code">
                <div class="label">LaTeX Source</div>
                <pre><?= htmlspecialchars($expr1) ?></pre>
            </div>
        </div>
    </div>

    <?php
    $expr2 = 'e^{i\\pi} + 1 = 0';
    $start = microtime(true);
    $result2 = $katex->call('render', ['expression' => $expr2, 'displayMode' => true]);
    $time2 = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #F7DF1E">
        <div class="example-header">
            <h2>Euler's Identity</h2>
            <span class="timing"><?= number_format($time2, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output" style="display: flex; align-items: center; justify-content: center;">
                <?= $result2 ?>
            </div>
            <div class="example-code">
                <div class="label">LaTeX Source</div>
                <pre><?= htmlspecialchars($expr2) ?></pre>
            </div>
        </div>
    </div>

    <?php
    $expr3 = '\\int_{-\\infty}^{\\infty} e^{-x^2} dx = \\sqrt{\\pi}';
    $start = microtime(true);
    $result3 = $katex->call('render', ['expression' => $expr3, 'displayMode' => true]);
    $time3 = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #F7DF1E">
        <div class="example-header">
            <h2>Integral</h2>
            <span class="timing"><?= number_format($time3, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output" style="display: flex; align-items: center; justify-content: center;">
                <?= $result3 ?>
            </div>
            <div class="example-code">
                <div class="label">LaTeX Source</div>
                <pre><?= htmlspecialchars($expr3) ?></pre>
            </div>
        </div>
    </div>

    <?php
    $expr4 = '\\begin{pmatrix} a & b \\\\ c & d \\end{pmatrix} \\begin{pmatrix} x \\\\ y \\end{pmatrix} = \\begin{pmatrix} ax + by \\\\ cx + dy \\end{pmatrix}';
    $start = microtime(true);
    $result4 = $katex->call('render', ['expression' => $expr4, 'displayMode' => true]);
    $time4 = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #F7DF1E">
        <div class="example-header">
            <h2>Matrix</h2>
            <span class="timing"><?= number_format($time4, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output" style="display: flex; align-items: center; justify-content: center;">
                <?= $result4 ?>
            </div>
            <div class="example-code">
                <div class="label">LaTeX Source</div>
                <pre><?= htmlspecialchars($expr4) ?></pre>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../_footer.php'; ?>
