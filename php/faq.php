<?php
require_once __DIR__ . '/includes/functions.php';
$PAGE_TITLE = ch('faq.hero_title') . ' — ' . ch('global.brand');
$PAGE_DESC = ch('faq.hero_subtitle');
$EXTRA_HEAD = '<style>.faq{max-width:820px;margin:0 auto;display:grid;gap:14px}.faq details{background:var(--card);border:1px solid var(--line);border-radius:14px;box-shadow:var(--shadow-sm);overflow:hidden}.faq summary{list-style:none;cursor:pointer;padding:20px 24px;font-family:var(--display);font-weight:600;font-size:1.08rem;color:var(--navy);display:flex;justify-content:space-between;gap:16px}.faq summary::-webkit-details-marker{display:none}.faq summary::after{content:"+";font-size:1.5rem;color:var(--teal-d)}.faq details[open] summary::after{content:"–"}.faq .ans{padding:0 24px 22px;color:var(--muted);line-height:1.7}</style>';
include __DIR__ . '/includes/head.php';
?>
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="<?= url('index.php') ?>">Home</a> <span>/</span> <span>FAQ</span></div>
    <h1><?= c('faq.hero_title') ?></h1>
    <p class="lead"><?= c('faq.hero_subtitle') ?></p>
  </div>
</section>
<section class="section">
  <div class="container">
    <div class="faq">
      <?php foreach (cj('faq.items') as $i => $f): ?>
        <details <?= $i === 0 ? 'open' : '' ?>>
          <summary><?= e($f['q'] ?? '') ?></summary>
          <div class="ans"><?= $f['a'] ?? '' ?></div>
        </details>
      <?php endforeach; ?>
    </div>
    <div class="text-center" style="margin-top:34px">
      <a class="btn btn-primary btn-lg" data-quote><?= icon('send') ?> Still have a question? Ask us</a>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
