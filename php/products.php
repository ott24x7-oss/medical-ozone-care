<?php
require_once __DIR__ . '/includes/functions.php';
$PAGE_TITLE = ch('products.hero_title') . ' — ' . ch('global.brand');
$PAGE_DESC = ch('products.hero_subtitle');
include __DIR__ . '/includes/head.php';

$cat = trim($_GET['category'] ?? '');
$qy = trim($_GET['q'] ?? '');
$all = products_all();
$list = array_filter($all, function ($p) use ($cat, $qy) {
    if ($cat && $cat !== 'All' && $p['category'] !== $cat) return false;
    if ($qy) {
        $hay = strtolower($p['title'] . ' ' . $p['model_number'] . ' ' . $p['category'] . ' ' . $p['short_description'] . ' ' . $p['tagline']);
        if (strpos($hay, strtolower($qy)) === false) return false;
    }
    return true;
});
$cats = array_merge(['All'], categories());
?>
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="<?= url('index.php') ?>">Home</a> <span>/</span> <span>Products</span></div>
    <h1><?= c('products.hero_title') ?></h1>
    <p class="lead"><?= c('products.hero_subtitle') ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <form class="search-bar" method="get" action="<?= url('products.php') ?>">
      <?php if ($cat && $cat !== 'All'): ?><input type="hidden" name="category" value="<?= e($cat) ?>"><?php endif; ?>
      <input type="search" name="q" value="<?= e($qy) ?>" placeholder="<?= c('products.search_placeholder') ?>" aria-label="Search products">
      <button class="btn btn-primary" type="submit"><?= icon('arrow') ?></button>
    </form>
    <div class="chips" style="justify-content:center;margin-bottom:36px">
      <?php foreach ($cats as $cc):
        $active = ($cat === $cc || ($cc === 'All' && $cat === '')) ? 'background:var(--teal-d);color:#fff;border-color:var(--teal-d)' : '';
        $href = $cc === 'All' ? url('products.php') : url('products.php?category=' . urlencode($cc)); ?>
        <a class="chip" href="<?= $href ?>" style="<?= $active ?>"><?= e($cc) ?></a>
      <?php endforeach; ?>
    </div>
    <div class="product-grid">
      <?php if ($list): foreach ($list as $p) echo product_card_html($p);
      else: ?>
        <p class="text-center" style="grid-column:1/-1;padding:40px;color:var(--muted)"><?= c('products.empty') ?> <a href="<?= url('products.php') ?>">Clear</a></p>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section tight bg-soft">
  <div class="container">
    <div class="cta-band">
      <div class="grid-2" style="align-items:center">
        <div><h2>Not sure which configuration you need?</h2><p>Tell us your cylinder type and application — we'll recommend the right setup.</p></div>
        <div style="text-align:right">
          <a class="btn btn-ghost btn-lg" data-quote>Request a Quote</a>
          <a class="btn btn-wa btn-lg" href="<?= wa_link() ?>" target="_blank" rel="noopener" style="margin-top:10px"><?= icon('wa', true) ?> WhatsApp Us</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
