<?php
require_once __DIR__ . '/includes/functions.php';
$PAGE_TITLE = ch('about.hero_title') . ' — ' . ch('global.brand');
$PAGE_DESC = ch('about.hero_subtitle');
include __DIR__ . '/includes/head.php';
?>
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="<?= url('index.php') ?>">Home</a> <span>/</span> <span>About</span></div>
    <h1><?= c('about.hero_title') ?></h1>
    <p class="lead"><?= c('about.hero_subtitle') ?></p>
  </div>
</section>

<section class="section">
  <div class="container benefit-grid">
    <div class="reveal">
      <span class="eyebrow"><?= c('about.story_eyebrow') ?></span>
      <h2><?= c('about.story_title') ?></h2>
      <?= ch('about.story') ?>
      <div class="hero-cta"><a class="btn btn-primary" href="<?= url('products.php') ?>">Explore Products <?= icon('arrow') ?></a><a class="btn btn-outline" data-quote>Talk to Us</a></div>
    </div>
    <div class="showcase-media reveal d1"><img src="<?= asset('img/products/device-card.svg') ?>" alt="AOT-MD-520 ozone generator"></div>
  </div>
</section>

<section class="section tight">
  <div class="container">
    <div class="stats">
      <?php foreach (cj('about.stats') as $i => $s): ?>
        <div class="stat reveal <?= $i ? 'd' . $i : '' ?>"><div class="num"><?= e($s['num'] ?? '') ?></div><div class="lbl"><?= e($s['label'] ?? '') ?></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section bg-ice">
  <div class="container">
    <div class="section-head reveal"><span class="eyebrow"><?= c('about.why_eyebrow') ?></span><h2><?= c('about.why_title') ?></h2></div>
    <div class="features">
      <?php foreach (cj('about.why') as $i => $w): ?>
        <div class="feature reveal <?= $i % 3 ? 'd' . ($i % 3) : '' ?>"><div class="ic"><?= icon($w['icon'] ?? 'badge') ?></div><h3><?= e($w['title'] ?? '') ?></h3><p><?= e($w['text'] ?? '') ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="contact">
  <div class="container">
    <div class="section-head reveal"><span class="eyebrow">Get in Touch</span><h2><?= c('about.contact_title') ?></h2><p class="lead"><?= c('about.contact_lead') ?></p></div>
    <div class="contact-grid">
      <div class="info-list reveal">
        <div class="info-item"><span class="ic"><?= icon('users') ?></span><div><div class="k">Brand</div><div class="v"><?= c('global.brand') ?></div></div></div>
        <div class="info-item"><span class="ic"><?= icon('phone') ?></span><div><div class="k">Phone / WhatsApp</div><div class="v"><a href="<?= tel_link() ?>"><?= c('global.phone') ?></a></div></div></div>
        <div class="info-item"><span class="ic"><?= icon('mail') ?></span><div><div class="k">Email</div><div class="v"><a href="mailto:<?= c('global.email2') ?>"><?= c('global.email2') ?></a><br><a href="mailto:<?= c('global.email1') ?>"><?= c('global.email1') ?></a></div></div></div>
        <div class="info-item"><span class="ic"><?= icon('pin') ?></span><div><div class="k">Address</div><div class="v"><?= c('global.address') ?></div></div></div>
      </div>
      <div class="form-card reveal d1">
        <h3 style="margin-bottom:6px"><?= c('contact.form_title') ?></h3>
        <p class="form-note" style="margin-bottom:16px"><?= c('contact.form_lead') ?></p>
        <form class="ajax-enquiry" novalidate>
          <div class="form-msg"></div>
          <div class="field hp" style="position:absolute;left:-9999px"><label>Company website</label><input type="text" name="company_website" tabindex="-1" autocomplete="off"></div>
          <div class="form-row">
            <div class="field"><label>Name *</label><input name="name" required placeholder="Full name"></div>
            <div class="field"><label>Phone *</label><input name="phone" required placeholder="Mobile" inputmode="tel"></div>
          </div>
          <div class="field"><label>Email</label><input name="email" type="email" placeholder="you@example.com"></div>
          <div class="form-row">
            <div class="field"><label>Enquiry type</label><select name="enquiry_type"><?php foreach (enquiry_types() as $t): ?><option><?= e($t) ?></option><?php endforeach; ?></select></div>
            <div class="field"><label>Product</label><select name="interested_product"><option value="">Select…</option><?php foreach (products_all() as $pp): ?><option><?= e($pp['title']) ?></option><?php endforeach; ?><option>Other</option></select></div>
          </div>
          <div class="field"><label>Message</label><textarea name="message" placeholder="Quantity, application, city…"></textarea></div>
          <input type="hidden" name="source" value="about-page">
          <button class="btn btn-primary btn-block btn-lg" type="submit"><?= icon('send') ?> Send Enquiry</button>
        </form>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
