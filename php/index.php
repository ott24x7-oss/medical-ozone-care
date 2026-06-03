<?php
require_once __DIR__ . '/includes/functions.php';
$PAGE_TITLE = ch('seo.title');
$EXTRA_HEAD = '<style>.hero .eyebrow .pulse{width:8px;height:8px;border-radius:50%;background:var(--cyan);display:inline-block}</style>';
include __DIR__ . '/includes/head.php';

$featured = array_slice(array_values(array_filter(products_all(), fn($p) => $p['category'] === 'Medical Ozone Generator')), 0, 3);
?>

<!-- HERO -->
<header class="hero">
  <div class="hero-bubbles"></div>
  <div class="container hero-grid">
    <div>
      <span class="eyebrow"><span class="pulse"></span> <?= c('home.hero_badge') ?></span>
      <h1><?= c('home.hero_title') ?> <span class="hl"><?= c('home.hero_title_hl') ?></span></h1>
      <p class="lead"><?= c('home.hero_subtitle') ?></p>
      <div class="hero-cta">
        <a class="btn btn-primary btn-lg" data-quote="Medical Ozone Generator AOT-MD-520"><?= c('home.cta_primary') ?> <?= icon('arrow') ?></a>
        <a href="<?= url('product.php?slug=aot-md-520') ?>" class="btn btn-blue btn-lg"><?= c('home.cta_secondary') ?></a>
        <a href="<?= tel_link() ?>" class="btn btn-ghost btn-lg"><?= icon('phone') ?> <?= c('home.cta_call') ?></a>
      </div>
      <div class="hero-trust">
        <?php foreach (cj('home.trust') as $i => $tr): $ic = ['checkc', 'shield', 'award'][$i] ?? 'checkc'; ?>
          <div class="t"><?= icon($ic) ?> <?= e($tr) ?></div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="hero-visual">
      <img class="device" src="<?= asset('img/products/device-hero.svg') ?>" alt="AOT-MD-520 medical ozone generator" width="1000" height="640" />
      <div class="hero-badge b1"><span class="n">4–92.5</span><span>mg/L<br>adjustable O₃</span></div>
      <div class="hero-badge b2"><span style="color:var(--teal-d)"><?= icon('zap') ?></span><span>Corona discharge<br>titanium + quartz</span></div>
    </div>
  </div>
</header>

<!-- FEATURES -->
<section class="section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow"><?= c('home.features_eyebrow') ?></span>
      <h2><?= c('home.features_title') ?></h2>
      <p class="lead"><?= c('home.features_lead') ?></p>
    </div>
    <div class="features">
      <?php foreach (cj('home.features') as $i => $f): ?>
        <div class="feature reveal <?= $i % 3 ? 'd' . ($i % 3) : '' ?>">
          <div class="ic"><?= icon($f['icon'] ?? 'badge') ?></div>
          <h3><?= e($f['title'] ?? '') ?></h3>
          <p><?= e($f['text'] ?? '') ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- SHOWCASE -->
<section class="section bg-ice">
  <div class="container showcase">
    <div class="showcase-media reveal"><img src="<?= img_url(ch('home.showcase_img', 'assets/img/products/device-top.svg')) ?>" alt="AOT-MD-520 control panel"></div>
    <div class="reveal d1">
      <span class="eyebrow"><?= c('home.showcase_eyebrow') ?></span>
      <h2><?= c('home.showcase_title') ?></h2>
      <p class="lead"><?= c('home.showcase_lead') ?></p>
      <ul class="checks">
        <?php foreach (cj('home.showcase_checks') as $ck): ?><li><?= icon('check') ?> <?= e($ck) ?></li><?php endforeach; ?>
      </ul>
      <div class="hero-cta" style="margin-top:8px">
        <a href="<?= url('product.php?slug=aot-md-520') ?>" class="btn btn-primary">View full specifications <?= icon('arrow') ?></a>
        <a class="btn btn-outline" data-quote="Medical Ozone Generator AOT-MD-520">Get pricing</a>
      </div>
    </div>
  </div>
</section>

<!-- STATS -->
<section class="section tight">
  <div class="container">
    <div class="stats">
      <?php foreach (cj('home.stats') as $i => $s):
        $num = $s['num'] ?? '';
        // animate pure numbers, else show as-is
        $isnum = is_numeric(rtrim($num, 'W')) || preg_match('/^\d/', $num); ?>
        <div class="stat reveal <?= $i ? 'd' . $i : '' ?>">
          <div class="num"><?= e($num) ?></div>
          <div class="lbl"><?= e($s['label'] ?? '') ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- PRODUCT RANGE -->
<section class="section bg-soft" id="products">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow"><?= c('home.products_eyebrow') ?></span>
      <h2><?= c('home.products_title') ?></h2>
      <p class="lead"><?= c('home.products_lead') ?></p>
    </div>
    <div class="product-grid">
      <?php foreach ($featured as $p) echo product_card_html($p); ?>
    </div>
    <div class="text-center" style="margin-top:40px">
      <a href="<?= url('products.php') ?>" class="btn btn-outline btn-lg"><?= c('home.products_cta') ?> <?= icon('arrow') ?></a>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow"><?= c('home.how_eyebrow') ?></span>
      <h2><?= c('home.how_title') ?></h2>
      <p class="lead"><?= c('home.how_lead') ?></p>
    </div>
    <div class="diagram-wrap reveal"><img src="<?= asset('img/products/diagram-connection.svg') ?>" alt="Connection overview" width="1000" height="420"></div>
    <div class="steps">
      <?php foreach (cj('home.steps') as $i => $st): ?>
        <div class="step reveal <?= $i ? 'd' . $i : '' ?>"><h3><?= e($st['title'] ?? '') ?></h3><p><?= e($st['text'] ?? '') ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- BENEFITS -->
<section class="section bg-ice">
  <div class="container benefit-grid">
    <div class="reveal">
      <span class="eyebrow"><?= c('home.benefits_eyebrow') ?></span>
      <h2><?= c('home.benefits_title') ?></h2>
      <p class="lead"><?= c('home.benefits_lead') ?></p>
      <ul class="benefit-list">
        <?php foreach (cj('home.benefits') as $b): ?>
          <li><span class="ic"><?= icon($b['icon'] ?? 'badge') ?></span><div><h4><?= e($b['title'] ?? '') ?></h4><p><?= e($b['text'] ?? '') ?></p></div></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="showcase-media reveal d1"><img src="<?= img_url(ch('home.benefits_img', 'assets/img/products/device-ports.svg')) ?>" alt="AOT-MD-520 rear ports"></div>
  </div>
</section>

<!-- CONCENTRATION CHART -->
<section class="section" id="chart">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow"><?= c('home.chart_eyebrow') ?></span>
      <h2><?= c('home.chart_title') ?></h2>
      <p class="lead"><?= c('home.chart_lead') ?></p>
    </div>
    <div class="reveal"><?= concentration_table_html() ?></div>
  </div>
</section>

<!-- APPLICATIONS -->
<section class="section bg-soft" id="applications">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow"><?= c('home.apps_eyebrow') ?></span>
      <h2><?= c('home.apps_title') ?></h2>
      <p class="lead"><?= c('home.apps_lead') ?></p>
    </div>
    <div class="apps-graphic reveal"><img src="<?= asset('img/products/applications.svg') ?>" alt="Medical ozone application areas" width="1100" height="420"></div>
    <div class="disclaimer-bar reveal"><strong>Disclaimer:</strong> <?= c('home.apps_disclaimer') ?></div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow"><?= c('home.tcards_eyebrow') ?></span>
      <h2><?= c('home.tcards_title') ?></h2>
    </div>
    <div class="tcards">
      <?php foreach (cj('home.testimonials') as $i => $t): $initials = strtoupper(substr($t['name'] ?? 'M', 0, 1) . substr(strstr($t['name'] ?? '', ' '), 1, 1)); ?>
        <div class="tcard reveal <?= $i ? 'd' . $i : '' ?>">
          <div class="stars">★★★★★</div>
          <p><?= e($t['quote'] ?? '') ?></p>
          <div class="who"><div class="av"><?= e(trim($initials) ?: 'M') ?></div><div><div class="nm"><?= e($t['name'] ?? '') ?></div><div class="rl"><?= e($t['role'] ?? '') ?></div></div></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CONTACT CTA -->
<section class="section" id="contact">
  <div class="container">
    <div class="cta-band reveal">
      <div class="grid-2" style="align-items:center;gap:40px">
        <div>
          <span class="eyebrow" style="background:rgba(255,255,255,.16);color:#fff;border-color:rgba(255,255,255,.25)">Get Started</span>
          <h2><?= c('home.cta_title') ?></h2>
          <p><?= c('home.cta_text') ?></p>
          <div class="hero-cta">
            <a class="btn btn-ghost btn-lg" href="<?= tel_link() ?>"><?= icon('phone') ?> <?= c('global.phone') ?></a>
            <a class="btn btn-wa btn-lg" href="<?= wa_link() ?>" target="_blank" rel="noopener"><?= icon('wa', true) ?> Chat on WhatsApp</a>
          </div>
        </div>
        <div class="form-card" style="background:#fff;color:var(--ink)">
          <h3 style="margin-bottom:6px"><?= c('home.cta_form_title') ?></h3>
          <p class="form-note" style="margin-bottom:16px"><?= c('home.cta_form_note') ?></p>
          <form class="ajax-enquiry" novalidate>
            <div class="form-msg"></div>
            <div class="field hp" style="position:absolute;left:-9999px"><label>Company website</label><input type="text" name="company_website" tabindex="-1" autocomplete="off"></div>
            <div class="form-row">
              <div class="field"><label>Name *</label><input name="name" required placeholder="Full name"></div>
              <div class="field"><label>Phone *</label><input name="phone" required placeholder="Mobile" inputmode="tel"></div>
            </div>
            <div class="field"><label>Email</label><input name="email" type="email" placeholder="you@example.com"></div>
            <div class="field"><label>Message</label><textarea name="message" placeholder="What are you looking for?"></textarea></div>
            <input type="hidden" name="enquiry_type" value="Quote Request">
            <input type="hidden" name="source" value="home-cta">
            <button class="btn btn-primary btn-block btn-lg" type="submit"><?= icon('send') ?> Send Enquiry</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
// move the homeMsg target into the form's message slot
include __DIR__ . '/includes/footer.php';
