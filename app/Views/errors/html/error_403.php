<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>403 - Access Forbidden | CPC RoomFinder</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="welcome-body">

  <div class="error-page-container">
    <div class="error-card">
      <div style="margin-bottom: 20px;">
        <img src="<?= base_url('assets/img/door-orange.svg') ?>" style="width: 58px; height: 58px;" alt="CPC Logo">
      </div>

      <div class="error-code" style="color: #ef4444;">403</div>
      <h1 class="error-title">Access Restricted</h1>
      
      <p class="error-desc">
        <?php if (ENVIRONMENT !== 'production' && ! empty($message)): ?>
          <?= esc($message) ?>
        <?php else: ?>
          You do not have permission to access this administrative portal or requested resource. Please sign in with an authorized institutional account.
        <?php endif ?>
      </p>

      <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
        <a href="<?= base_url('logout') ?>" class="btn btn-orange" style="padding: 12px 30px; font-weight: 700;">
          Switch Account
        </a>
        <a href="<?= base_url() ?>" class="btn btn-navy" style="padding: 12px 30px;">
          Return to Home
        </a>
      </div>
    </div>
  </div>

</body>
</html>

