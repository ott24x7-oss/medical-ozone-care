<?php
require_once __DIR__ . '/includes/functions.php';
$docs = ['terms', 'privacy', 'disclaimer', 'warranty', 'usage'];
$doc = strtolower(trim($_GET['doc'] ?? 'terms'));
if (!in_array($doc, $docs, true)) $doc = 'terms';
$title = ch('legal.' . $doc . '_title', 'Legal');
$body = ch('legal.' . $doc . '_body', '<p>Not found.</p>');
$PAGE_TITLE = $title . ' — ' . ch('global.brand');
$EXTRA_HEAD = '<style>.legal-doc{max-width:820px;margin:0 auto;background:var(--card);border:1px solid var(--line);border-radius:var(--radius-lg);padding:clamp(26px,4vw,48px);box-shadow:var(--shadow)}.legal-doc h3{margin:28px 0 8px;color:var(--navy);font-size:1.2rem}.legal-doc p{color:var(--ink);line-height:1.75}.legal-nav{display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin-bottom:30px}.legal-nav a{padding:8px 16px;border-radius:100px;border:1px solid var(--line);background:#fff;color:var(--muted);font-weight:600;font-size:.9rem}.legal-nav a.active{background:var(--teal-d);color:#fff;border-color:var(--teal-d)}</style>';
include __DIR__ . '/includes/head.php';
?>
<section class="page-hero" style="padding-bottom:50px">
  <div class="container">
    <div class="breadcrumb"><a href="<?= url('index.php') ?>">Home</a> <span>/</span> <span><?= e($title) ?></span></div>
    <h1><?= e($title) ?></h1>
    <p class="lead">Please read the following information carefully.</p>
  </div>
</section>
<section class="section">
  <div class="container">
    <nav class="legal-nav">
      <?php foreach ($docs as $d): ?>
        <a href="<?= url('legal.php?doc=' . $d) ?>" class="<?= $d === $doc ? 'active' : '' ?>"><?= e(ch('legal.' . $d . '_title')) ?></a>
      <?php endforeach; ?>
    </nav>
    <article class="legal-doc"><?= $body ?></article>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
