<?php
require_once __DIR__ . '/../includes/auth.php';
start_session();
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (admin_login($_POST['email'] ?? '', $_POST['password'] ?? '')) {
        redirect(url('admin/index.php'));
    }
    $err = 'Invalid email or password.';
}
if (is_admin()) redirect(url('admin/index.php'));
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Sign-in — Medical Ozone Care</title>
<meta name="robots" content="noindex,nofollow">
<link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
</head><body style="background:#eef4f7">
<div style="min-height:100vh;display:grid;place-items:center;padding:20px">
  <div class="form-card" style="max-width:400px;width:100%">
    <div style="text-align:center;margin-bottom:8px"><img src="<?= asset('img/logo.svg') ?>" alt="Medical Ozone Care" style="height:46px;margin:0 auto"></div>
    <h3 style="text-align:center;margin:6px 0 4px">Admin sign-in</h3>
    <p class="form-note" style="text-align:center;margin-bottom:18px">Manage products, enquiries &amp; site content.</p>
    <?php if ($err): ?><div class="form-msg err"><?= e($err) ?></div><?php endif; ?>
    <form method="post">
      <div class="field"><label>Email</label><input name="email" type="email" required autocomplete="username" placeholder="admin email"></div>
      <div class="field"><label>Password</label><input name="password" type="password" required autocomplete="current-password" placeholder="password"></div>
      <button class="btn btn-primary btn-block btn-lg" type="submit">Sign in</button>
    </form>
    <p class="form-note" style="text-align:center;margin-top:14px"><a href="<?= url('index.php') ?>">← Back to website</a></p>
  </div>
</div>
</body></html>
