<footer class="footer-comunigest py-4">
    <div class="container text-center">
        <small>
            &copy; <?= e(date('Y')) ?> ComuniGest
        </small>
    </div>
</footer>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

<?php foreach (($scripts ?? []) as $script): ?>

    <script src="<?= e(url($script)) ?>"></script>

<?php endforeach; ?>

</body>
</html>