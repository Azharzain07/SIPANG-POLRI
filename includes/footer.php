    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> - <?php echo APP_AUTHOR; ?></p>
        <p>Version <?php echo APP_VERSION; ?> | Sistem Informasi Perencanaan Anggaran Kepolisian</p>
    </footer>

    <!-- Global JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <!-- Page Specific JavaScript -->
    <?php if (isset($page_js)): ?>
        <script src="assets/js/<?php echo $page_js; ?>"></script>
    <?php endif; ?>
</body>
</html>

