</div>

<script src="/assets/js/search.js"></script>
<script src="/assets/js/feed.js"></script>

<?php if (!empty($pageScripts)): ?>

    <?php foreach ($pageScripts as $script): ?>

        <script src="<?= htmlspecialchars($script) ?>"></script>

    <?php endforeach; ?>

<?php endif; ?>

</body>
</html>