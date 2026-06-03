<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$ADMIN_TITLE = $ADMIN_TITLE ?? 'Admin';
$cur = current_file();
$grp = $_GET['group'] ?? '';
function flash($msg = null, $type = 'ok')
{
    if ($msg !== null) { $_SESSION['flash'] = ['m' => $msg, 't' => $type]; return; }
    if (empty($_SESSION['flash'])) return '';
    $f = $_SESSION['flash']; unset($_SESSION['flash']);
    return '<div class="form-msg ' . e($f['t']) . '" style="display:block">' . e($f['m']) . '</div>';
}
$NAV = [
    ['index.php', '', 'Dashboard', 'gauge'],
    ['products.php', '', 'Products', 'badge'],
    ['enquiries.php', '', 'Enquiries', 'mail'],
    ['brochures.php', '', 'Brochures', 'doc'],
    ['content.php', '', 'Site Content', 'sliders'],
    ['content.php', 'Global', 'Contact Details', 'phone'],
    ['content.php', 'SEO', 'SEO Settings', 'globe'],
    ['content.php', 'Legal', 'Legal Pages', 'shield'],
];
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($ADMIN_TITLE) ?> — Admin</title>
<meta name="robots" content="noindex,nofollow">
<link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
<style>
  body{background:#eef4f7}
  .ash{display:grid;grid-template-columns:248px 1fr;min-height:100vh}
  .side{background:var(--navy-d);color:#c5d6df;padding:22px 16px;position:sticky;top:0;height:100vh;overflow:auto}
  .side .brand img{height:38px;margin-bottom:22px}
  .side nav{display:grid;gap:3px}
  .side nav a{display:flex;align-items:center;gap:11px;color:#b9cad4;text-decoration:none;font-weight:600;padding:11px 13px;border-radius:10px;font-size:.95rem}
  .side nav a svg{width:18px;height:18px}
  .side nav a:hover{background:rgba(255,255,255,.06);color:#fff}
  .side nav a.active{background:var(--teal-d);color:#fff}
  .side .sep{height:1px;background:rgba(255,255,255,.08);margin:12px 4px}
  .main{padding:24px clamp(16px,3vw,38px);max-width:1180px}
  .atop{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:22px;flex-wrap:wrap}
  .atop h1{font-size:1.6rem;margin:0}.who{color:var(--muted);font-size:.9rem}
  .a-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px;margin-bottom:24px}
  .a-stat{background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;box-shadow:var(--shadow-sm)}
  .a-stat .n{font-family:var(--display);font-weight:800;font-size:2rem;color:var(--teal-d)}.a-stat .l{color:var(--muted);font-size:.9rem;font-weight:600}
  .panel{background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow-sm);padding:20px;margin-bottom:20px}
  .panel h2{font-size:1.15rem;margin:0 0 14px}
  .at{width:100%;border-collapse:collapse}.at th,.at td{text-align:left;padding:11px 12px;border-bottom:1px solid var(--line);font-size:.9rem;vertical-align:top}
  .at th{background:#f3fafb;color:var(--navy);font-weight:700}.at img{height:40px;width:54px;object-fit:contain;background:var(--grad-soft);border-radius:6px}
  .mini{padding:7px 12px;font-size:.82rem;border-radius:9px}
  .pill{padding:3px 10px;border-radius:100px;font-size:.76rem;font-weight:700;display:inline-block}
  .pill.active,.pill.New{background:#e6f8f4;color:var(--teal-d)}.pill.inactive{background:#eef1f4;color:var(--muted)}
  .pill.Contacted{background:#fff7e8;color:var(--amber)}.pill.Quoted{background:#eaf2ff;color:var(--blue)}.pill.Closed{background:#eef9f2;color:var(--green)}
  .fld{margin-bottom:13px}.fld label{display:block;font-weight:600;font-size:.85rem;color:var(--navy);margin-bottom:5px}
  .fld input,.fld select,.fld textarea{width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font:inherit;font-size:.92rem}
  .fld textarea{min-height:80px;resize:vertical}.fld .hint{color:var(--muted);font-size:.78rem;margin-top:3px}
  .grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
  @media(max-width:820px){.ash{grid-template-columns:1fr}.side{position:static;height:auto}.grid2{grid-template-columns:1fr}}
</style>
</head><body>
<div class="ash">
  <aside class="side">
    <div class="brand"><img src="<?= asset('img/logo-light.svg') ?>" alt="Medical Ozone Care"></div>
    <nav>
      <?php foreach ($NAV as [$f, $g, $label, $ic]):
        $active = ($cur === $f && (($g === '' && $grp === '' && $f !== 'content.php') || ($g !== '' && $grp === $g) || ($f === 'content.php' && $g === '' && $grp === '')));
        $href = url('admin/' . $f . ($g ? '?group=' . urlencode($g) : '')); ?>
        <a href="<?= $href ?>" class="<?= $active ? 'active' : '' ?>"><?= icon($ic) ?> <?= e($label) ?></a>
      <?php endforeach; ?>
      <div class="sep"></div>
      <a href="<?= url('index.php') ?>" target="_blank"><?= icon('globe') ?> View site ↗</a>
      <a href="<?= url('admin/logout.php') ?>"><?= icon('x') ?> Sign out</a>
    </nav>
  </aside>
  <main class="main">
    <div class="atop"><h1><?= e($ADMIN_TITLE) ?></h1><span class="who">Signed in as <?= e(current_admin_email()) ?></span></div>
    <?= flash() ?>
