    </div><!-- /.content-area -->

    <footer class="text-center text-muted small py-3 border-top bg-white">
      &copy; <?= date('Y') ?> EIRE Tax Refunds &middot; MB Tax Group &middot; Admin Panel v1.0
    </footer>
  </div><!-- /.main-col -->
</div><!-- /.app-shell -->

<?php $flash = get_flash(); if ($flash): ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1080;">
  <div class="toast align-items-center text-white bg-<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'danger' : 'teal') ?> border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body">
        <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle' : 'bi-info-circle' ?> me-1"></i>
        <?= h($flash['message']) ?>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Bootstrap 5 JS bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/script.js"></script>
<?php if (!empty($extraScripts)) echo $extraScripts; ?>
</body>
</html>
