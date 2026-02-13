    <!-- JavaScript -->
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    <?php if (isset($extraJS)): ?>
        <?php foreach ($extraJS as $js): ?>
            <script src="<?= base_url($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
