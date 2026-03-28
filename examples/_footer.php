
<script>
document.addEventListener('keydown', function(e) {
<?php if (!empty($_prev)): ?>
    if (e.key === 'ArrowLeft') window.location.href = <?= json_encode($_prev['url']) ?>;
<?php endif; ?>
<?php if (!empty($_next)): ?>
    if (e.key === 'ArrowRight') window.location.href = <?= json_encode($_next['url']) ?>;
<?php endif; ?>
});
</script>
    <footer>hack'd by <a href="https://bsky.app/profile/johanjanssens.bsky.social" target="_blank">Johan Janssens</a></footer>
</body>
</html>
