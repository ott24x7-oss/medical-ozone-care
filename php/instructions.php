<?php
require_once __DIR__ . '/includes/functions.php';
$PAGE_TITLE = ch('instructions.hero_title') . ' — ' . ch('global.brand');
$PAGE_DESC = ch('instructions.hero_subtitle');
$EXTRA_HEAD = '<style>@media print{#site-header,.topbar,.footer,.fab,#quoteModal,.page-hero .btn{display:none!important}.page-hero{background:#0b3a52!important}}</style>';
include __DIR__ . '/includes/head.php';
?>
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="<?= url('index.php') ?>">Home</a> <span>/</span> <span>Instructions</span></div>
    <h1><?= c('instructions.hero_title') ?></h1>
    <p class="lead"><?= c('instructions.hero_subtitle') ?></p>
    <button class="btn btn-ghost" style="margin-top:18px" onclick="window.print()"><?= icon('doc') ?> Print / Save as PDF</button>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:900px">
    <?= ch('instructions.body') ?>
    <div class="doc-section" id="chart">
      <h2><span class="n"><?= icon('gauge') ?></span> <?= c('instructions.chart_title') ?></h2>
      <p>Find your oxygen flow rate (left) and read across to your selected level. Modes: <strong>M1</strong> = L1–L4, <strong>M2</strong> = L5–L6.</p>
      <?= concentration_table_html() ?>
    </div>
    <div class="callout info" style="margin-top:24px"><?= icon('headset') ?><div><strong>Need help?</strong> Call <a href="<?= tel_link() ?>"><?= c('global.phone') ?></a> or <a href="<?= wa_link() ?>" target="_blank" rel="noopener">WhatsApp us</a> for first-time setup support.</div></div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
