</main>
<footer class="bg-light border-top mt-auto">
  <div class="container py-4 small text-muted d-flex flex-wrap gap-3 justify-content-between">
    <div>© <?php echo date('Y'); ?> royalfree.com</div>
    <div class="d-flex gap-3 align-items-center">
      <a class="link-secondary text-decoration-none" href="#">Impressum</a>
      <a class="link-secondary text-decoration-none" href="#">About</a>
      <?php if (!isAdminLoggedIn() && !isUserLoggedIn()): ?>
          <a href="admin_login.php" class="btn btn-dark btn-sm ms-2">Admin-Login</a>
      <?php endif; ?>
    </div>
  </div>
</footer>
</body>
</html>
