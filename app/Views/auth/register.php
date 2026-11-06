<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account - CPC RoomFinder | Colegio De la Purisima Concepcion</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="register-page" style="background-image: url('<?= base_url('assets/img/classroom_bg.jpg') ?>');">

  <div class="register-overlay"></div>

  <!-- Header Bar -->
  <header class="cpc-navbar">
    <a class="brand" href="<?= base_url() ?>" id="regBrandLink">
      <img class="brand-icon" src="<?= base_url('assets/img/door-orange.svg') ?>" alt="CPC RoomFinder Logo">
      <span>CPC RoomFinder</span>
    </a>

    <div class="school-badge">
      <img class="seal-icon" src="<?= base_url('assets/img/cpc-seal.svg') ?>" alt="Colegio De la Purisima Concepcion Seal">
      <span>Colegio De la Purisima Concepcion</span>
    </div>
  </header>

  <!-- Centered Wizard Container -->
  <main class="register-content">
    <h1 class="register-title" id="wizardTitle">Let's get you started!</h1>

    <div class="register-card">
      <!-- Stepper Pills Navigation -->
      <nav class="stepper-nav" aria-label="Registration Progress">
        <div class="stepper-pill-container">
          <button type="button" class="step-pill active" id="pillPersonalDetails">Personal Details</button>
          <button type="button" class="step-pill" id="pillAuthentication">Authentication</button>
        </div>
      </nav>

      <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; font-size: 0.88rem; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
          <strong>Please correct the following errors:</strong>
          <ul style="margin: 6px 0 0 18px; padding: 0;">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
              <li><?= esc($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php elseif (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; font-size: 0.88rem; padding: 10px 14px; border-radius: 8px; margin-bottom: 20px;">
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <form 
        id="registerWizardForm" 
        method="POST" 
        action="<?= base_url('register') ?>" 
        data-login-url="<?= base_url('login') ?>"
      >
        <?= csrf_field() ?>

        <!-- STEP 1: Personal Details -->
        <div id="regStep1">
          <!-- Row 1: Names and Role selection -->
          <div class="register-form-grid">
            <div class="reg-field">
              <label for="regFirstname">Firstname</label>
              <input type="text" class="reg-input" id="regFirstname" name="firstname" value="<?= esc(old('firstname')) ?>" placeholder="John" required>
            </div>

            <div class="reg-field">
              <label for="regLastname">Lastname</label>
              <input type="text" class="reg-input" id="regLastname" name="lastname" value="<?= esc(old('lastname')) ?>" placeholder="Doe" required>
            </div>

            <div class="reg-field">
              <label for="regRoleSelect">Registering as:</label>
              <select class="reg-select" id="regRoleSelect" name="role">
                <option value="student" <?= (old('role', $initialRole ?? '') === 'instructor') ? '' : 'selected' ?>>Student / Instructor (Student)</option>
                <option value="instructor" <?= (old('role', $initialRole ?? '') === 'instructor') ? 'selected' : '' ?>>Student / Instructor (Instructor)</option>
              </select>
            </div>
          </div>

          <!-- Dynamic Row 2 for STUDENT -->
          <div id="studentSpecificFields" class="register-form-grid">
            <div class="reg-field">
              <label for="regStudentId">Student ID Number</label>
              <input type="text" class="reg-input" id="regStudentId" name="student_id" value="<?= esc(old('student_id')) ?>" placeholder="202401001">
            </div>

            <div class="reg-field">
              <label for="regCourse">Course</label>
              <select class="reg-select" id="regCourse" name="course">
                <option value="BSIT" <?= old('course') === 'BSIT' ? 'selected' : '' ?>>BS Information Technology (BSIT)</option>
                <option value="BSCS" <?= old('course') === 'BSCS' ? 'selected' : '' ?>>BS Computer Science (BSCS)</option>
                <option value="BSCE" <?= old('course') === 'BSCE' ? 'selected' : '' ?>>BS Civil Engineering (BSCE)</option>
                <option value="BSEMC" <?= old('course') === 'BSEMC' ? 'selected' : '' ?>>BS Entertainment and Multimedia Computing</option>
                <option value="BSBA" <?= old('course') === 'BSBA' ? 'selected' : '' ?>>BS Business Administration</option>
              </select>
            </div>

            <div class="reg-field">
              <label for="regYearLevel">Year / Level</label>
              <select class="reg-select" id="regYearLevel" name="year_level">
                <option value="1st Year" <?= old('year_level') === '1st Year' ? 'selected' : '' ?>>1st Year</option>
                <option value="2nd Year" <?= old('year_level') === '2nd Year' ? 'selected' : '' ?>>2nd Year</option>
                <option value="3rd Year" <?= old('year_level') === '3rd Year' ? 'selected' : '' ?>>3rd Year</option>
                <option value="4th Year" <?= old('year_level') === '4th Year' ? 'selected' : '' ?>>4th Year</option>
              </select>
            </div>
          </div>

          <!-- Dynamic Row 2 for INSTRUCTOR -->
          <div id="instructorSpecificFields" class="register-form-grid two-cols" style="display: none;">
            <div class="reg-field">
              <label for="regEmployeeId">Employee ID No.</label>
              <input type="text" class="reg-input" id="regEmployeeId" name="employee_id" value="<?= esc(old('employee_id')) ?>" placeholder="202400645">
            </div>

            <div class="reg-field">
              <label for="regDepartment">Department</label>
              <select class="reg-select" id="regDepartment" name="department">
                <?php if (!empty($departments)): ?>
                  <?php foreach ($departments as $d): ?>
                    <option value="<?= esc($d['code']) ?>" <?= old('department') === $d['code'] ? 'selected' : '' ?>>
                      <?= esc($d['name']) ?> (<?= esc($d['code']) ?>)
                    </option>
                  <?php endforeach; ?>
                <?php else: ?>
                  <option value="CET">College of Engineering &amp; Technology (CET)</option>
                  <option value="CASE">College of Arts, Sciences &amp; Education (CASE)</option>
                  <option value="CBA">College of Business Administration (CBA)</option>
                  <option value="Marine">College of Maritime Education</option>
                  <option value="Psychology">Psychology Department</option>
                  <option value="IT">Information Technology (IT)</option>
                <?php endif; ?>
              </select>
            </div>
          </div>

          <!-- Row 3: Email (Full Width) -->
          <div class="register-form-grid full-width" style="margin-top: 16px;">
            <div class="reg-field">
              <label for="regEmail">Email</label>
              <input type="email" class="reg-input" id="regEmail" name="email" value="<?= esc(old('email')) ?>" placeholder="JohnDoe@email.com" required>
            </div>
          </div>

          <!-- Footer Actions for Step 1 -->
          <div class="register-footer">
            <a href="<?= base_url('login') ?>" class="cancel-link" id="cancelStep1Link">Cancel Registration</a>
            <button type="button" class="btn btn-navy" id="btnNextToAuth">Next</button>
          </div>
        </div>

        <!-- STEP 2: Authentication / Finishing Up -->
        <div id="regStep2" style="display: none;">
          <div class="register-form-grid full-width" style="gap: 20px;">
            <div class="reg-field">
              <label for="regPassword">Create your Password</label>
              <input type="password" class="reg-input" id="regPassword" name="password" placeholder="••••••••••••" required>
            </div>

            <div class="reg-field">
              <label for="regPasswordConfirm">Confirm Password:</label>
              <input type="password" class="reg-input" id="regPasswordConfirm" name="password_confirm" placeholder="••••••••••••" required>
            </div>
          </div>

          <div class="reg-note">
            <strong>Note:</strong> Your username will be your 
            <span class="role-highlight-text">Student ID / Employee ID</span>. 
            Use it every time you log in.
          </div>

          <!-- Footer Actions for Step 2 -->
          <div class="register-footer">
            <a href="<?= base_url('login') ?>" class="cancel-link" id="cancelStep2Link">Cancel Registration</a>
            <div style="display: flex; gap: 12px;">
              <button type="button" class="btn btn-navy" style="background:#475569;" id="btnBackToPersonal">Back</button>
              <button type="submit" class="btn btn-navy" id="btnSubmitRegister">Register</button>
            </div>
          </div>
        </div>

      </form>
    </div>
  </main>

  <!-- Registration Success Modal -->
  <div class="cpc-modal-backdrop" id="regSuccessModal" role="dialog" aria-modal="true" aria-labelledby="modalSuccessTitle">
    <div class="cpc-modal-dialog" style="text-align: center;">
      <button type="button" class="modal-close-btn" data-modal-close aria-label="Close dialog">&times;</button>
      <div style="font-size: 3.5rem; margin-bottom: 12px;">🎉</div>
      <h2 class="modal-title" id="modalSuccessTitle" style="color: var(--cpc-lime);">Registration Complete!</h2>
      <p style="color: #cbd5e1; margin-bottom: 18px; font-size: 0.95rem;">
        Welcome to CPC RoomFinder! Your account has been initialized.
      </p>
      
      <div style="background: rgba(255,255,255,0.06); border-radius: 12px; padding: 16px; margin-bottom: 24px; text-align: left;">
        <div style="font-size: 0.85rem; color: #94a3b8;">Assigned Username (ID No.):</div>
        <div style="font-size: 1.3rem; font-weight: 700; color: #ffffff;" id="regSuccessId">202401001</div>
        <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 8px;">Role:</div>
        <div style="font-size: 1rem; font-weight: 600; color: var(--cpc-orange);" id="regSuccessRole">STUDENT</div>
      </div>

      <div style="display: flex; gap: 14px; justify-content: center;">
        <a href="<?= base_url('login') ?>" class="btn btn-orange" style="padding: 12px 36px;">Proceed to Log In</a>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
