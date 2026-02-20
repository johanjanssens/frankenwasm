<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'JSX / React SSR';
$_prev = ['url' => '../enhance-ssr/', 'label' => 'Enhance SSR'];
$_next = ['url' => '../html-rewriter/', 'label' => 'HTML Rewriter'];
include __DIR__ . '/../_header.php';
?>

    <div class="intro">
        <div class="intro-badges">
            <span class="badge badge-js">JS</span>
            <?php if (isset($_sizes['jsx-include'])): ?><span class="badge badge-size"><?= _fmtSize($_sizes['jsx-include']) ?></span><?php endif; ?>
        </div>
        <h1>JSX / React SSR</h1>
        <p>Server-side render React components via <code>$jsx->call('render', ['script' => ..., 'props' => ...])</code></p>
    </div>

    <?php $jsx = new Wasm('jsx-include'); ?>

    <?php
    // Example 1: Simple greeting
    $script1 = 'function App(props) { return <h3>Hello, {props.name}!</h3> }';
    $input1 = ['script' => $script1, 'props' => ['name' => 'FrankenWASM']];

    $totalTime = 0;
    $result1 = '';
    for ($i = 0; $i < 10; $i++) {
        $start = microtime(true);
        $result1 = $jsx->call('render', $input1);
        $totalTime += microtime(true) - $start;
    }
    $avgTime1 = ($totalTime / 10) * 1000;
    ?>
    <div class="example" style="border-top: 4px solid #F7DF1E">
        <div class="example-header">
            <h2>Simple Greeting</h2>
            <span class="timing">avg <?= number_format($avgTime1, 2) ?> ms (10 runs)</span>
        </div>
        <div class="example-sections">
            <div class="example-output">
                <div class="label">Rendered Output</div>
                <?= $result1 ?>
            </div>
            <div class="example-code">
                <div class="label">JSX Component</div>
                <pre><?= htmlspecialchars(<<<'CODE'
function App(props) {
  return <h3>Hello, {props.name}!</h3>
}

// props: { name: "FrankenWASM" }
CODE) ?></pre>
            </div>
        </div>
    </div>

    <?php
    // Example 2: Component with list
    $script2 = '
function App(props) {
  return (
    <div>
      <h3>{props.title}</h3>
      <ul>
        {props.items.map(function(item, i) {
          return <li key={i}>{item}</li>
        })}
      </ul>
    </div>
  )
}';
    $input2 = [
        'script' => $script2,
        'props' => [
            'title' => 'WASM Plugin Languages',
            'items' => ['Go', 'Rust', 'JavaScript', 'C/C++', 'AssemblyScript'],
        ],
    ];

    $start = microtime(true);
    $result2 = $jsx->call('render', $input2);
    $time2 = (microtime(true) - $start) * 1000;
    ?>
    <div class="example" style="border-top: 4px solid #F7DF1E">
        <div class="example-header">
            <h2>List Component</h2>
            <span class="timing"><?= number_format($time2, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output">
                <div class="label">Rendered Output</div>
                <?= $result2 ?>
            </div>
            <div class="example-code">
                <div class="label">JSX Component</div>
                <pre><?= htmlspecialchars(<<<'CODE'
function App(props) {
  return (
    <div>
      <h3>{props.title}</h3>
      <ul>
        {props.items.map((item, i) =>
          <li key={i}>{item}</li>
        )}
      </ul>
    </div>
  )
}

// props: {
//   title: "WASM Plugin Languages",
//   items: ["Go", "Rust", "JavaScript", ...]
// }
CODE) ?></pre>
            </div>
        </div>
    </div>

    <?php
    // Example 3: Styled card
    $script3 = '
function App(props) {
  var style = {
    border: "1px solid #e2e8f0",
    borderRadius: "8px",
    padding: "20px",
    maxWidth: "400px",
    boxShadow: "0 2px 8px rgba(0,0,0,0.1)"
  };
  return (
    <div style={style}>
      <h3 style={{color: "#2c3e50", marginBottom: "8px"}}>{props.title}</h3>
      <p style={{color: "#666"}}>{props.body}</p>
      <small style={{color: "#999"}}>Rendered server-side via WASM</small>
    </div>
  )
}';
    $input3 = [
        'script' => $script3,
        'props' => [
            'title' => 'Server-Side React',
            'body' => 'This card was rendered on the server using React JSX compiled through a WebAssembly plugin, called from PHP.',
        ],
    ];

    $start = microtime(true);
    $result3 = $jsx->call('render', $input3);
    $time3 = (microtime(true) - $start) * 1000;
    ?>
    <div class="example" style="border-top: 4px solid #F7DF1E">
        <div class="example-header">
            <h2>Styled Card</h2>
            <span class="timing"><?= number_format($time3, 2) ?> ms</span>
        </div>
        <div class="example-sections">
            <div class="example-output">
                <div class="label">Rendered Output</div>
                <?= $result3 ?>
            </div>
            <div class="example-code">
                <div class="label">JSX Component</div>
                <pre><?= htmlspecialchars(<<<'CODE'
function App(props) {
  var style = {
    border: "1px solid #e2e8f0",
    borderRadius: "8px",
    padding: "20px",
    boxShadow: "0 2px 8px rgba(0,0,0,0.1)"
  };
  return (
    <div style={style}>
      <h3 style={{color: "#2c3e50"}}>{props.title}</h3>
      <p style={{color: "#666"}}>{props.body}</p>
      <small>Rendered server-side via WASM</small>
    </div>
  )
}

// props: {
//   title: "Server-Side React",
//   body: "This card was rendered..."
// }
CODE) ?></pre>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../_footer.php'; ?>
