<?php
require_once __DIR__ . '/includes/functions.php';
$slug = trim($_GET['slug'] ?? '');
$p = $slug ? product_by_slug($slug) : null;
if (!$p || $p['status'] !== 'active') {
    http_response_code(404);
    $PAGE_TITLE = 'Product not found — ' . ch('global.brand');
    include __DIR__ . '/includes/head.php';
    echo '<section class="section" style="padding-top:140px"><div class="container text-center"><h1>Product not found</h1><p class="lead">This product isn\'t available.</p><a class="btn btn-primary" href="' . url('products.php') . '">Browse all products</a></div></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}
$PAGE_TITLE = $p['title'] . ' (' . $p['model_number'] . ') — ' . ch('global.brand');
$PAGE_DESC = $p['short_description'];
$OG_IMAGE = img_url($p['image']);

// JSON-LD
$ld = [
    '@context' => 'https://schema.org/', '@type' => 'Product', 'name' => $p['title'],
    'sku' => $p['model_number'], 'mpn' => $p['model_number'], 'description' => $p['short_description'],
    'brand' => ['@type' => 'Brand', 'name' => 'Medical Ozone Care'],
    'image' => [img_url($p['image'])],
    'offers' => ['@type' => 'Offer', 'priceCurrency' => 'INR', 'availability' => 'https://schema.org/InStock',
        'price' => $p['price_value'] ?: null, 'seller' => ['@type' => 'Organization', 'name' => 'Medical Ozone Care']],
];
$EXTRA_HEAD = '<script type="application/ld+json">' . json_encode($ld, JSON_UNESCAPED_SLASHES) . '</script>';
include __DIR__ . '/includes/head.php';

$gallery = !empty($p['images']) ? $p['images'] : [$p['image']];
$related = array_slice(array_filter(products_all(), fn($x) => $x['slug'] !== $p['slug']), 0, 3);
?>
<section class="page-hero" style="padding-bottom:40px">
  <div class="container"><div class="breadcrumb"><a href="<?= url('index.php') ?>">Home</a> <span>/</span> <a href="<?= url('products.php') ?>">Products</a> <span>/</span> <span><?= e($p['title']) ?></span></div></div>
</section>

<section class="section" style="padding-top:48px">
  <div class="container">
    <div class="pd-grid">
      <div class="pd-gallery">
        <div class="main"><img id="mainImg" src="<?= img_url($gallery[0]) ?>" alt="<?= e($p['title']) ?>"></div>
        <?php if (count($gallery) > 1): ?>
          <div class="pd-thumbs">
            <?php foreach ($gallery as $i => $g): ?>
              <button class="<?= $i === 0 ? 'active' : '' ?>" data-img="<?= img_url($g) ?>"><img src="<?= img_url($g) ?>" alt="view <?= $i + 1 ?>"></button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="pd-info">
        <span class="cat"><?= e($p['category']) ?></span>
        <h1><?= e($p['title']) ?></h1>
        <div class="model">Model: <?= e($p['model_number']) ?></div>
        <div class="chips"><?php foreach ($p['highlights'] as $h): ?><span class="chip"><?= e($h) ?></span><?php endforeach; ?></div>
        <ul class="pd-highlights"><?php foreach (array_slice($p['highlights'], 0, 5) as $h): ?><li><?= icon('checkc') ?> <?= e($h) ?></li><?php endforeach; ?></ul>
        <div class="price-row">
          <?php if ($p['price_value']): ?><span class="amt"><?= e($p['price']) ?></span><span class="cur">incl. unit · taxes/delivery extra</span>
          <?php else: ?><span class="amt" style="font-size:1.2rem"><?= e($p['price']) ?></span><span class="cur">share requirement for a quote</span><?php endif; ?>
        </div>
        <div class="chips" style="margin:0 0 18px"><span class="status-dot">In stock</span> <?php if ($p['warranty'] && $p['warranty'] !== '—'): ?><span class="chip"><?= e($p['warranty']) ?> warranty</span><?php endif; ?></div>
        <div class="pd-actions">
          <a class="btn btn-primary btn-lg" data-quote="<?= e($p['title']) ?>"><?= icon('send') ?> Request a Quote</a>
          <a class="btn btn-wa btn-lg" href="<?= wa_link($p['title']) ?>" target="_blank" rel="noopener"><?= icon('wa', true) ?> WhatsApp</a>
          <a class="btn btn-outline btn-lg" href="<?= tel_link() ?>"><?= icon('phone') ?> Call</a>
          <?php if (!empty($p['brochure_pdf'])): ?><a class="btn btn-outline btn-lg" href="<?= url('brochure.php?slug=' . urlencode($p['slug'])) ?>" target="_blank" rel="noopener"><?= icon('doc') ?> <?= c('product.cta_brochure') ?></a><?php endif; ?>
        </div>
      </div>
    </div>

    <div class="tabs" id="tabs">
      <button class="active" data-tab="overview"><?= c('product.tab_overview') ?></button>
      <button data-tab="specs"><?= c('product.tab_specs') ?></button>
      <?php $hasBox = $p['accessories'] || $p['items']; if ($hasBox): ?><button data-tab="box"><?= c('product.tab_box') ?></button><?php endif; ?>
      <?php if ($p['has_concentration_table']): ?><button data-tab="chart"><?= c('product.tab_chart') ?></button><?php endif; ?>
    </div>
    <div>
      <div class="tabpane active" id="tab-overview">
        <p class="lead"><?= e($p['full_description']) ?></p>
        <?php if ($p['features']): ?><div class="features" style="margin-top:24px">
          <?php foreach ($p['features'] as $f): ?><div class="feature"><div class="ic"><?= icon($f['icon'] ?? 'badge') ?></div><h3><?= e($f['title'] ?? '') ?></h3><p><?= e($f['text'] ?? '') ?></p></div><?php endforeach; ?>
        </div><?php endif; ?>
      </div>
      <div class="tabpane" id="tab-specs">
        <table class="spec-table"><tbody>
          <?php foreach ($p['specifications'] as $k => $v): ?><tr><th><?= e($k) ?></th><td><?= e($v) ?></td></tr><?php endforeach; ?>
        </tbody></table>
      </div>
      <?php if ($hasBox): ?>
      <div class="tabpane" id="tab-box">
        <ul class="pd-highlights" style="margin-bottom:24px">
          <?php foreach ($p['accessories'] as $a): ?><li><?= icon('check') ?> <?= e($a['name']) ?> <strong>× <?= e($a['qty']) ?></strong></li><?php endforeach; ?>
        </ul>
        <?php if ($p['items']): ?><div class="acc-items">
          <?php foreach ($p['items'] as $it): ?><div class="acc-item"><img src="<?= img_url($it['img']) ?>" alt="<?= e($it['name']) ?>"><div class="nm"><?= e($it['name']) ?></div><div class="qty"><?= e($it['qty']) ?></div></div><?php endforeach; ?>
        </div><?php endif; ?>
      </div>
      <?php endif; ?>
      <?php if ($p['has_concentration_table']): ?><div class="tabpane" id="tab-chart"><?= concentration_table_html() ?></div><?php endif; ?>
    </div>
  </div>
</section>

<?php if ($related): ?>
<section class="section tight bg-soft">
  <div class="container">
    <div class="section-head"><span class="eyebrow">Complete the Set</span><h2><?= c('product.related_title') ?></h2></div>
    <div class="product-grid"><?php foreach ($related as $r) echo product_card_html($r); ?></div>
  </div>
</section>
<?php endif; ?>

<script>
(function(){
  document.querySelectorAll('.pd-thumbs button').forEach(function(b){
    b.addEventListener('click',function(){
      document.getElementById('mainImg').src=b.dataset.img;
      document.querySelectorAll('.pd-thumbs button').forEach(function(x){x.classList.remove('active');});
      b.classList.add('active');
    });
  });
  document.querySelectorAll('#tabs button').forEach(function(b){
    b.addEventListener('click',function(){
      document.querySelectorAll('#tabs button').forEach(function(x){x.classList.remove('active');});
      document.querySelectorAll('.tabpane').forEach(function(x){x.classList.remove('active');});
      b.classList.add('active');
      document.getElementById('tab-'+b.dataset.tab).classList.add('active');
    });
  });
})();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
