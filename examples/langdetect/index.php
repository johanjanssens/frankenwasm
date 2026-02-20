<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'Language Detect';
$_prev = ['url' => '../ascii/', 'label' => 'ASCII Art'];
$_next = ['url' => '../markdown/', 'label' => 'Markdown'];
$_styleExtra = <<<'CSS'
        .container { max-width: 900px; }
        .lang-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 16px;
        }
        .lang-card blockquote {
            margin: 0 0 12px;
            padding: 12px 16px;
            background: #f8f9fa;
            border-left: 4px solid #6366f1;
            border-radius: 0 4px 4px 0;
            font-style: italic;
            color: #555;
        }
        .lang-result {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }
        .lang-result .lang-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a1a2e;
        }
        .lang-result .lang-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-confidence {
            background: #dcfce7;
            color: #166534;
        }
        .badge-script {
            background: #e0e7ff;
            color: #3730a3;
        }
        .badge-code {
            background: #f3f4f6;
            color: #6b7280;
            font-family: monospace;
        }
        .badge-reliable {
            background: #fef9c3;
            color: #854d0e;
        }
        html.dark .lang-card { background: #16213e; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        html.dark .lang-card blockquote { background: #1e2a45; color: #cdd6f4; border-left-color: #6366f1; }
        html.dark .lang-result .lang-name { color: #e0e0e0; }
        html.dark .badge-confidence { background: #1a332e; color: #4ade80; }
        html.dark .badge-script { background: #1e1a45; color: #a5b4fc; }
        html.dark .badge-code { background: #1e2a45; color: #8b95a5; }
        html.dark .badge-reliable { background: #332e1a; color: #fbbf24; }
CSS;
include __DIR__ . '/../_header.php';
?>

    <div class="intro">
        <div class="intro-badges">
            <span class="badge badge-rust">Rust</span>
            <?php if (isset($_sizes['langdetect-rs'])): ?><span class="badge badge-size"><?= _fmtSize($_sizes['langdetect-rs']) ?></span><?php endif; ?>
        </div>
        <h1>Language Detection</h1>
        <p>Detect the language of any text using <code>$lang->call('detect', $text)</code> — powered by the <code>whatlang</code> crate</p>
    </div>

    <?php $lang = new Wasm('langdetect-rs'); ?>

    <div class="container">
        <div class="panel" style="border-top: 5px solid #6366f1">
            <h2>Multi-Language Detection</h2>
            <?php
            $samples = [
                'The quick brown fox jumps over the lazy dog. This is a sample English text for language detection.',
                'Le renard brun rapide saute par-dessus le chien paresseux. Ceci est un exemple de texte en français.',
                'Der schnelle braune Fuchs springt über den faulen Hund. Dies ist ein deutscher Beispieltext.',
                'El rápido zorro marrón salta sobre el perro perezoso. Este es un texto de ejemplo en español.',
                '素早い茶色のキツネは怠けた犬を飛び越えます。これは日本語のサンプルテキストです。',
                'Быстрая коричневая лиса прыгает через ленивую собаку. Это пример текста на русском языке.',
                '快速的棕色狐狸跳过了懒狗。这是一个中文示例文本，用于语言检测。',
                'A rápida raposa marrom pula sobre o cão preguiçoso. Este é um texto de exemplo em português.',
                'De snelle bruine vos springt over de luie hond. Dit is een voorbeeld tekst in het Nederlands.',
                'Hızlı kahverengi tilki tembel köpeğin üzerinden atlar. Bu bir Türkçe örnek metindir.',
                'السريع البني الثعلب يقفز فوق الكلب الكسول. هذا نص عربي نموذجي لاكتشاف اللغة.',
                'दुनिया के सभी मनुष्य जन्म से स्वतंत्र तथा मर्यादा और अधिकारों में समान हैं।',
            ];

            foreach ($samples as $sample):
                $start = microtime(true);
                $result = $lang->call('detect', $sample);
                $time = (microtime(true) - $start) * 1000;
                $data = is_array($result) ? $result : json_decode($result, true);
            ?>
                <div class="lang-card">
                    <blockquote><?= htmlspecialchars(mb_substr($sample, 0, 100)) ?><?= mb_strlen($sample) > 100 ? '...' : '' ?></blockquote>
                    <div class="lang-result">
                        <span class="lang-name"><?= htmlspecialchars($data['language_name'] ?? 'Unknown') ?></span>
                        <span class="lang-badge badge-code"><?= htmlspecialchars($data['language_code'] ?? '?') ?></span>
                        <span class="lang-badge badge-script"><?= htmlspecialchars($data['script'] ?? '?') ?></span>
                        <span class="lang-badge badge-confidence"><?= number_format(($data['confidence'] ?? 0) * 100, 1) ?>%</span>
                        <?php if ($data['is_reliable'] ?? false): ?>
                            <span class="lang-badge badge-reliable">Reliable</span>
                        <?php endif; ?>
                        <span class="lang-badge badge-code" style="margin-left: auto;"><?= number_format($time, 2) ?> ms</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

<?php include __DIR__ . '/../_footer.php'; ?>
