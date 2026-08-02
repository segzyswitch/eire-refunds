document.addEventListener('DOMContentLoaded', function () {
  var body = document.body;

  // Desktop collapse toggle
  var collapseBtn = document.getElementById('sidebarCollapseBtn');
  if (collapseBtn) {
    collapseBtn.addEventListener('click', function () {
      body.classList.toggle('sidebar-collapsed');
      localStorage.setItem('etr_sidebar_collapsed', body.classList.contains('sidebar-collapsed') ? '1' : '0');
    });
    if (localStorage.getItem('etr_sidebar_collapsed') === '1') {
      body.classList.add('sidebar-collapsed');
    }
  }

  // Mobile off-canvas toggle
  var mobileBtn = document.getElementById('sidebarMobileBtn');
  var backdrop = document.querySelector('.sidebar-backdrop');
  function closeMobile() { body.classList.remove('sidebar-mobile-open'); }
  if (mobileBtn) {
    mobileBtn.addEventListener('click', function () {
      body.classList.toggle('sidebar-mobile-open');
    });
  }
  if (backdrop) {
    backdrop.addEventListener('click', closeMobile);
  }

  // Auto-dismiss toasts
  document.querySelectorAll('.toast').forEach(function (el) {
    var toast = new bootstrap.Toast(el, { delay: 4000 });
    toast.show();
  });

  // Password strength meter (used on security page)
  var pwInput = document.getElementById('new_password');
  var pwMeter = document.getElementById('passwordStrengthBar');
  var pwLabel = document.getElementById('passwordStrengthLabel');
  if (pwInput && pwMeter) {
    pwInput.addEventListener('input', function () {
      var val = pwInput.value;
      var score = 0;
      if (val.length >= 8) score++;
      if (/[A-Z]/.test(val)) score++;
      if (/[0-9]/.test(val)) score++;
      if (/[^A-Za-z0-9]/.test(val)) score++;
      var levels = [
        { width: '10%', cls: 'bg-danger', text: 'Very weak' },
        { width: '35%', cls: 'bg-danger', text: 'Weak' },
        { width: '65%', cls: 'bg-warning', text: 'Okay' },
        { width: '85%', cls: 'bg-success', text: 'Good' },
        { width: '100%', cls: 'bg-success', text: 'Strong' }
      ];
      var level = levels[score] || levels[0];
      pwMeter.style.width = val.length ? level.width : '0%';
      pwMeter.className = 'progress-bar ' + level.cls;
      pwLabel.textContent = val.length ? level.text : '';
    });
  }

  // Toggle visibility for password fields
  document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var target = document.getElementById(btn.getAttribute('data-toggle-password'));
      if (!target) return;
      var icon = btn.querySelector('i');
      if (target.type === 'password') {
        target.type = 'text';
        icon && icon.classList.replace('bi-eye', 'bi-eye-slash');
      } else {
        target.type = 'password';
        icon && icon.classList.replace('bi-eye-slash', 'bi-eye');
      }
    });
  });

  // Generic confirm-delete modal wiring: any [data-confirm-delete] button
  // fills in the shared #confirmDeleteModal and points its form action.
  var confirmModalEl = document.getElementById('confirmDeleteModal');
  if (confirmModalEl) {
    var confirmModal = new bootstrap.Modal(confirmModalEl);
    var form = confirmModalEl.querySelector('form');
    var nameSlot = confirmModalEl.querySelector('[data-confirm-name]');
    document.querySelectorAll('[data-confirm-delete]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        form.action = btn.getAttribute('data-action');
        if (nameSlot) nameSlot.textContent = btn.getAttribute('data-name') || 'this item';
        confirmModal.show();
      });
    });
  }

  // Live char-counter for textareas with [data-char-count]
  document.querySelectorAll('[data-char-count]').forEach(function (el) {
    var counter = document.querySelector(el.getAttribute('data-char-count'));
    function update() { if (counter) counter.textContent = el.value.length; }
    el.addEventListener('input', update);
    update();
  });
});
