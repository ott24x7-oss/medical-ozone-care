<?php
require_once __DIR__ . '/includes/functions.php';
$PAGE_TITLE = ch('contact.hero_title') . ' — ' . ch('global.brand');
$PAGE_DESC = ch('contact.hero_subtitle');
include __DIR__ . '/includes/head.php';
$sent = isset($_GET['sent']);
?>
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="<?= url('index.php') ?>">Home</a> <span>/</span> <span>Contact</span></div>
    <h1><?= c('contact.hero_title') ?></h1>
    <p class="lead"><?= c('contact.hero_subtitle') ?></p>
  </div>
</section>

<section class="section tight">
  <div class="container">
    <div class="features">
      <a class="feature reveal" href="<?= tel_link() ?>" style="text-decoration:none"><div class="ic"><?= icon('phone') ?></div><h3><?= c('contact.tile_call_title') ?></h3><p style="color:var(--navy);font-weight:600"><?= c('global.phone') ?></p><p><?= c('global.hours') ?></p></a>
      <a class="feature reveal d1" href="<?= wa_link() ?>" target="_blank" rel="noopener" style="text-decoration:none"><div class="ic" style="background:#25d366"><?= icon('wa', true) ?></div><h3><?= c('contact.tile_wa_title') ?></h3><p style="color:var(--navy);font-weight:600">Chat instantly</p><p>Fastest way to reach us</p></a>
      <a class="feature reveal d2" href="mailto:<?= c('global.email2') ?>" style="text-decoration:none"><div class="ic"><?= icon('mail') ?></div><h3><?= c('contact.tile_email_title') ?></h3><p style="color:var(--navy);font-weight:600"><?= c('global.email2') ?></p><p>We reply within a day</p></a>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0" id="contact">
  <div class="container">
    <div class="contact-grid">
      <div class="reveal">
        <h2><?= c('contact.form_title') ?></h2>
        <p class="lead"><?= c('contact.form_lead') ?></p>
        <div class="form-card" style="margin-top:18px">
          <?php if ($sent): ?><div class="form-msg ok"><?= c('contact.success') ?></div><?php endif; ?>
          <form class="ajax-enquiry" method="post" action="<?= url('api/enquiry.php') ?>" novalidate>
            <div class="form-msg"></div>
            <div class="field hp" style="position:absolute;left:-9999px"><label>Company website</label><input type="text" name="company_website" tabindex="-1" autocomplete="off"></div>
            <div class="form-row">
              <div class="field"><label>Name *</label><input name="name" required placeholder="Full name"></div>
              <div class="field"><label>Phone *</label><input name="phone" required placeholder="Mobile" inputmode="tel"></div>
            </div>
            <div class="field"><label>Email</label><input name="email" type="email" placeholder="you@example.com"></div>
            <div class="form-row">
              <div class="field"><label>Enquiry type</label><select name="enquiry_type"><?php foreach (enquiry_types() as $t): ?><option><?= e($t) ?></option><?php endforeach; ?></select></div>
              <div class="field"><label>Product of interest</label><select name="interested_product"><option value="">Select…</option><?php foreach (products_all() as $pp): ?><option><?= e($pp['title']) ?></option><?php endforeach; ?><option>Other / Not sure</option></select></div>
            </div>
            <div class="field"><label>Message</label><textarea name="message" placeholder="Quantity, application, your city…"></textarea></div>
            <input type="hidden" name="source" value="contact-page">
            <button class="btn btn-primary btn-block btn-lg" type="submit"><?= icon('send') ?> Send Enquiry</button>
          </form>
        </div>
      </div>
      <div class="reveal d1">
        <h2><?= c('contact.info_title') ?></h2>
        <div class="info-list" style="margin-bottom:20px">
          <div class="info-item"><span class="ic"><?= icon('users') ?></span><div><div class="k">Brand</div><div class="v"><?= c('global.brand') ?></div></div></div>
          <div class="info-item"><span class="ic"><?= icon('pin') ?></span><div><div class="k">Address</div><div class="v"><?= c('global.address') ?></div></div></div>
          <div class="info-item"><span class="ic"><?= icon('phone') ?></span><div><div class="k">Phone</div><div class="v"><a href="<?= tel_link() ?>"><?= c('global.phone') ?></a></div></div></div>
          <div class="info-item"><span class="ic"><?= icon('mail') ?></span><div><div class="k">Email</div><div class="v"><a href="mailto:<?= c('global.email1') ?>"><?= c('global.email1') ?></a> · <a href="mailto:<?= c('global.email2') ?>"><?= c('global.email2') ?></a></div></div></div>
        </div>
        <div style="border-radius:var(--radius);overflow:hidden;border:1px solid var(--line);box-shadow:var(--shadow-sm)">
          <iframe title="Map" width="100%" height="320" style="border:0;display:block" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q=<?= urlencode(ch('contact.map_query')) ?>&output=embed"></iframe>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
