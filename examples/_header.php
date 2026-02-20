<?php
/**
 * Shared header template for FrankenWASM demo pages.
 *
 * Expected variables:
 *   $_title      string         Page title (used in <title> and can be used in intro)
 *   $_prev       array|null     ['url' => '...', 'label' => '...'] for prev nav link
 *   $_next       array|null     ['url' => '...', 'label' => '...'] for next nav link
 *   $_headExtra  string|null    Extra HTML for <head> (e.g. external CSS)
 *   $_styleExtra string|null    Page-specific CSS (inserted in <style> block)
 *
 * Provides:
 *   $_sizes      array          Plugin name => file size in bytes
 *   _fmtSize()   function       Format bytes to human-readable string
 */

use FrankenPHP\Wasm;

$_meta = Wasm::metadata();
$_sizes = [];
foreach ($_meta as $_m) $_sizes[$_m['name']] = $_m['file_size'];

function _fmtSize($b) {
    if ($b >= 1048576) return number_format($b / 1048576, 1) . ' MB';
    if ($b >= 1024) return number_format($b / 1024, 0) . ' KB';
    return $b . ' B';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>(function(){var t=localStorage.getItem('theme'),d=window.matchMedia('(prefers-color-scheme:dark)').matches;if(t==='dark'||(!t&&d))document.documentElement.classList.add('dark')})()</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($_title) ?> - FrankenWASM</title>
    <link rel="stylesheet" href="/style.php">
<?= $_headExtra ?? '' ?>
<?php if (!empty($_styleExtra)): ?>
    <style>
<?= $_styleExtra ?>
    </style>
<?php endif; ?>
</head>
<body>
    <div class="nav">
        <a href="../">&larr; Back to demos</a>
        <button class="theme-toggle" onclick="document.documentElement.classList.toggle('dark');localStorage.setItem('theme',document.documentElement.classList.contains('dark')?'dark':'light')" aria-label="Toggle theme">&#x25D1;</button>
        <span class="nav-links">
<?php if (!empty($_prev)): ?>
            <a href="<?= $_prev['url'] ?>">&larr; <?= $_prev['label'] ?></a>
<?php endif; ?>
<?php if (!empty($_next)): ?>
            <a href="<?= $_next['url'] ?>"><?= $_next['label'] ?> &rarr;</a>
<?php endif; ?>
        </span>
    </div>
