/**
 * CPC RoomFinder - Frontend Logic & Interactive Controller
 * Colegio De la Purisima Concepcion
 */

document.addEventListener('DOMContentLoaded', () => {
  initMobileDrawer();
  initRegisterWizard();
  initLoginForm();
  initModals();
});

/* ==========================================================================
   Mobile Navigation Drawer
   ========================================================================== */
function initMobileDrawer() {
  const menuBtn = document.querySelector('.menu-btn');
  const drawer = document.getElementById('cpcDrawer');
  const backdrop = document.getElementById('drawerBackdrop');
  const closeBtn = document.getElementById('drawerClose');

  if (!menuBtn || !drawer || !backdrop) return;

  function openDrawer() {
    drawer.classList.add('open');
    backdrop.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    drawer.classList.remove('open');
    backdrop.classList.remove('active');
    document.body.style.overflow = '';
  }

  menuBtn.addEventListener('click', openDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  backdrop.addEventListener('click', closeDrawer);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && drawer.classList.contains('open')) {
      closeDrawer();
    }
  });
}

/* ==========================================================================
   Multi-Step Registration Wizard
   ========================================================================== */
function initRegisterWizard() {
  const form = document.getElementById('registerWizardForm');
  if (!form) return;

  const roleSelect = document.getElementById('regRoleSelect');
  const studentFields = document.getElementById('studentSpecificFields');
  const instructorFields = document.getElementById('instructorSpecificFields');
  
  const step1Container = document.getElementById('regStep1');
  const step2Container = document.getElementById('regStep2');
  const pillPersonal = document.getElementById('pillPersonalDetails');
  const pillAuth = document.getElementById('pillAuthentication');
  const wizardTitle = document.getElementById('wizardTitle');
  
  const btnNext = document.getElementById('btnNextToAuth');
  const btnBack = document.getElementById('btnBackToPersonal');

  // Switch role dynamic fields
  function updateRoleFields(role) {
    if (role === 'instructor') {
      if (studentFields) studentFields.style.display = 'none';
      if (instructorFields) instructorFields.style.display = 'contents';
      const roleDisplay = document.querySelector('.role-highlight-text');
      if (roleDisplay) roleDisplay.textContent = 'Instructor';
    } else {
      if (studentFields) studentFields.style.display = 'contents';
      if (instructorFields) instructorFields.style.display = 'none';
      const roleDisplay = document.querySelector('.role-highlight-text');
      if (roleDisplay) roleDisplay.textContent = 'Student';
    }
  }

  if (roleSelect) {
    roleSelect.addEventListener('change', (e) => {
      updateRoleFields(e.target.value);
    });
    // Set initial state
    updateRoleFields(roleSelect.value);
  }

  // Stepper Transition: Step 1 -> Step 2
  function goToStep(stepNumber) {
    if (stepNumber === 2) {
      // Validate Step 1 first
      const currentRole = roleSelect ? roleSelect.value : 'student';
      const firstname = form.querySelector('[name="firstname"]')?.value.trim();
      const lastname = form.querySelector('[name="lastname"]')?.value.trim();
      const email = form.querySelector('[name="email"]')?.value.trim();
      
      let idValue = '';
      if (currentRole === 'student') {
        idValue = form.querySelector('[name="student_id"]')?.value.trim();
      } else {
        idValue = form.querySelector('[name="employee_id"]')?.value.trim();
      }

      if (!firstname || !lastname) {
        showToast('Please provide your First name and Last name.', 'error');
        return;
      }
      if (!idValue) {
        showToast('Please enter your ID Number.', 'error');
        return;
      }

      // Switch views
      step1Container.style.display = 'none';
      step2Container.style.display = 'block';
      
      pillPersonal.classList.remove('active');
      pillAuth.classList.add('active');
      
      if (wizardTitle) wizardTitle.textContent = 'Finishing Up';
    } else {
      // Return to Step 1
      step1Container.style.display = 'block';
      step2Container.style.display = 'none';
      
      pillPersonal.classList.add('active');
      pillAuth.classList.remove('active');
      
      if (wizardTitle) wizardTitle.textContent = "Let's get you started!";
    }
  }

  if (btnNext) {
    btnNext.addEventListener('click', (e) => {
      e.preventDefault();
      goToStep(2);
    });
  }

  if (btnBack) {
    btnBack.addEventListener('click', (e) => {
      e.preventDefault();
      goToStep(1);
    });
  }

  if (pillPersonal) {
    pillPersonal.addEventListener('click', (e) => {
      e.preventDefault();
      goToStep(1);
    });
  }

  if (pillAuth) {
    pillAuth.addEventListener('click', (e) => {
      e.preventDefault();
      goToStep(2);
    });
  }

  // Handle final registration form submit
  form.addEventListener('submit', (e) => {
    const pass1 = form.querySelector('[name="password"]')?.value;
    const pass2 = form.querySelector('[name="password_confirm"]')?.value;

    if (!pass1 || pass1.length < 6) {
      e.preventDefault();
      showToast('Password must be at least 6 characters long.', 'error');
      return;
    }

    if (pass1 !== pass2) {
      e.preventDefault();
      showToast('Passwords do not match. Please verify.', 'error');
      return;
    }

    // If form has a backend action, submit it
    if (form.getAttribute('action')) {
      return true;
    }

    e.preventDefault();
    const currentRole = roleSelect ? roleSelect.value : 'student';
    const idVal = currentRole === 'student' 
      ? form.querySelector('[name="student_id"]')?.value || '202401001'
      : form.querySelector('[name="employee_id"]')?.value || '202400645';

    // Show simulated success modal
    const modal = document.getElementById('regSuccessModal');
    if (modal) {
      const regIdSpan = document.getElementById('regSuccessId');
      const regRoleSpan = document.getElementById('regSuccessRole');
      if (regIdSpan) regIdSpan.textContent = idVal;
      if (regRoleSpan) regRoleSpan.textContent = currentRole.toUpperCase();
      openModal(modal);
    } else {
      showToast('Registration successful! Redirecting to login...', 'success');
      setTimeout(() => {
        window.location.href = form.dataset.loginUrl || '/login';
      }, 1500);
    }
  });
}

/* ==========================================================================
   Login Form & Role Simulator
   ========================================================================== */
function initLoginForm() {
  const loginForm = document.getElementById('cpcLoginForm');
  if (!loginForm) return;

  const usernameInput = document.getElementById('loginUsername');
  const passwordInput = document.getElementById('loginPassword');
  const togglePassBtn = document.getElementById('togglePasswordBtn');
  const eyeIconShow = document.getElementById('eyeIconShow');
  const eyeIconHide = document.getElementById('eyeIconHide');
  const roleChips = document.querySelectorAll('.role-chip');

  // Show / Hide Password toggle
  if (togglePassBtn && passwordInput) {
    togglePassBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const isPassword = passwordInput.getAttribute('type') === 'password';
      passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

      if (eyeIconShow && eyeIconHide) {
        eyeIconShow.style.display = isPassword ? 'none' : 'inline-block';
        eyeIconHide.style.display = isPassword ? 'inline-block' : 'none';
      }

      const label = isPassword ? 'Hide password' : 'Show password';
      togglePassBtn.setAttribute('title', label);
      togglePassBtn.setAttribute('aria-label', label);
      togglePassBtn.style.color = isPassword ? 'var(--cpc-orange)' : '#64748b';
    });
  }

  // Preset demo accounts for reviewer convenience
  const demoAccounts = {
    student: {
      user: '202401001',
      pass: 'purisima2024',
      target: loginForm.dataset.studentUrl || '/student/dashboard'
    },
    instructor: {
      user: '202400645',
      pass: 'instructor2024',
      target: loginForm.dataset.instructorUrl || '/instructor/bookings'
    },
    admin: {
      user: 'admin',
      pass: 'admin123',
      target: loginForm.dataset.adminUrl || '/admin/overview'
    }
  };

  roleChips.forEach(chip => {
    chip.addEventListener('click', () => {
      const role = chip.dataset.role;
      if (demoAccounts[role]) {
        if (usernameInput) usernameInput.value = demoAccounts[role].user;
        if (passwordInput) passwordInput.value = demoAccounts[role].pass;

        roleChips.forEach(c => {
          c.style.background = '';
          c.style.borderColor = '';
        });
        chip.style.background = 'var(--cpc-orange)';
        chip.style.borderColor = 'var(--cpc-orange)';

        showToast(`Auto-filled credentials for ${role.toUpperCase()}`, 'success');
      }
    });
  });

  loginForm.addEventListener('submit', (e) => {
    const user = usernameInput?.value.trim() || '';
    const pass = passwordInput?.value || '';

    if (!user) {
      e.preventDefault();
      showToast('Please enter your Username or Student/Employee ID.', 'error');
      return;
    }
    if (!pass) {
      e.preventDefault();
      showToast('Please enter your Password.', 'error');
      return;
    }

    // If backend action URL is defined on the form, submit to backend
    if (loginForm.getAttribute('action')) {
      return true;
    }

    e.preventDefault();
    // Role detection heuristic for frontend demo fallback
    let destination = loginForm.dataset.studentUrl || '/student/dashboard';
    if (user.toLowerCase().includes('admin') || user === '1001') {
      destination = loginForm.dataset.adminUrl || '/admin/overview';
    } else if (user.startsWith('202400') || user.toLowerCase().includes('prof') || user.toLowerCase().includes('inst')) {
      destination = loginForm.dataset.instructorUrl || '/instructor/bookings';
    }

    showToast('Login successful! Loading dashboard...', 'success');
    setTimeout(() => {
      window.location.href = destination;
    }, 700);
  });
}

/* ==========================================================================
   Modals Engine
   ========================================================================== */
function initModals() {
  // Modal triggers
  document.querySelectorAll('[data-modal-open]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const modalId = btn.dataset.modalOpen;
      const modal = document.getElementById(modalId);
      if (modal) openModal(modal);
    });
  });

  // Close buttons
  document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const modal = btn.closest('.cpc-modal-backdrop');
      if (modal) closeModal(modal);
    });
  });

  // Backdrop click to close
  document.querySelectorAll('.cpc-modal-backdrop').forEach(modal => {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        closeModal(modal);
      }
    });
  });

  // ESC key to close
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const activeModal = document.querySelector('.cpc-modal-backdrop.active');
      if (activeModal) closeModal(activeModal);
    }
  });
}

function openModal(modalEl) {
  if (!modalEl) return;
  modalEl.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeModal(modalEl) {
  if (!modalEl) return;
  modalEl.classList.remove('active');
  document.body.style.overflow = '';
}

/* ==========================================================================
   Toast Notification System
   ========================================================================== */
function showToast(message, type = 'info') {
  let container = document.querySelector('.cpc-toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'cpc-toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `cpc-toast ${type}`;
  
  let iconSvg = 'ℹ️';
  if (type === 'success') iconSvg = '✅';
  if (type === 'error') iconSvg = '⚠️';

  toast.innerHTML = `<span>${iconSvg}</span><span>${message}</span>`;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.transition = 'all 0.3s ease';
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => toast.remove(), 300);
  }, 3500);
}
