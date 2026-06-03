<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="brand"><img src="<?= asset('img/logo-light.svg') ?>" alt="<?= c('global.brand') ?>" width="200" height="52"></div>
        <p class="muted"><?= c('footer.about') ?></p>
        <div class="social">
          <a href="<?= wa_link() ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><?= icon('wa', true) ?></a>
          <a href="mailto:<?= c('global.email2') ?>" aria-label="Email"><?= icon('mail') ?></a>
          <a href="<?= tel_link() ?>" aria-label="Call"><?= icon('phone') ?></a>
        </div>
      </div>
      <div>
        <h4><?= c('footer.products_title') ?></h4>
        <ul>
          <li><a href="<?= url('product.php?slug=aot-md-520') ?>">AOT-MD-520 Generator</a></li>
          <li><a href="<?= url('product.php?slug=ozone-generator-digital') ?>">Digital Generator</a></li>
          <li><a href="<?= url('product.php?slug=ozone-water-oil-system') ?>">Ozone Water &amp; Oil</a></li>
          <li><a href="<?= url('products.php') ?>">All Products</a></li>
        </ul>
      </div>
      <div>
        <h4><?= c('footer.company_title') ?></h4>
        <ul>
          <li><a href="<?= url('about.php') ?>">About Us</a></li>
          <li><a href="<?= url('faq.php') ?>">FAQ</a></li>
          <li><a href="<?= url('instructions.php') ?>">Instructions</a></li>
          <li><a href="<?= url('legal.php?doc=terms') ?>">Terms &amp; Conditions</a></li>
          <li><a href="<?= url('legal.php?doc=privacy') ?>">Privacy Policy</a></li>
          <li><a href="<?= url('legal.php?doc=warranty') ?>">Warranty Policy</a></li>
          <li><a href="<?= url('legal.php?doc=disclaimer') ?>">Disclaimer</a></li>
        </ul>
      </div>
      <div>
        <h4><?= c('footer.contact_title') ?></h4>
        <ul class="foot-contact">
          <li><?= icon('pin') ?><span><?= c('global.address') ?></span></li>
          <li><?= icon('phone') ?><a href="<?= tel_link() ?>"><?= c('global.phone') ?></a></li>
          <li><?= icon('mail') ?><a href="mailto:<?= c('global.email2') ?>"><?= c('global.email2') ?></a></li>
          <li><?= icon('clock') ?><span><?= c('global.hours') ?></span></li>
        </ul>
      </div>
    </div>
    <p class="muted" style="font-size:.82rem;margin-top:18px;border-top:1px solid rgba(255,255,255,.08);padding-top:16px">
      <strong style="color:#cbd9e2">Disclaimer:</strong> <?= c('global.disclaimer') ?>
    </p>
    <div class="footer-bottom">
      <span>© <?= date('Y') ?> <?= c('global.brand') ?>. <?= c('footer.bottom') ?></span>
      <span><?= c('global.website') ?> · <a href="<?= url('legal.php?doc=usage') ?>">Usage Disclaimer</a></span>
    </div>
  </div>
</footer>

<!-- Floating WhatsApp -->
<a class="fab" href="<?= wa_link() ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"><?= icon('wa', true) ?></a>

<!-- Quote modal -->
<div id="quoteModal" style="position:fixed;inset:0;z-index:200;display:none;align-items:center;justify-content:center;padding:20px">
  <div class="qm-backdrop" style="position:absolute;inset:0;background:rgba(8,42,60,.55)"></div>
  <div class="form-card" role="dialog" aria-modal="true" style="position:relative;max-width:520px;width:100%;max-height:92vh;overflow:auto">
    <button id="qmClose" aria-label="Close" style="position:absolute;top:14px;right:14px;background:#f1f6f8;border:0;border-radius:10px;width:38px;height:38px;cursor:pointer;color:var(--navy)"><?= icon('x') ?></button>
    <span class="eyebrow"><?= icon('send') ?> Request a Quote</span>
    <h3 style="margin:6px 0 4px">Tell us what you need</h3>
    <p class="form-note" style="margin-bottom:16px">We'll reply within one business day with pricing and availability.</p>
    <div class="form-msg" id="qmMsg"></div>
    <form id="qmForm" novalidate>
      <div class="field hp" style="position:absolute;left:-9999px"><label>Company website</label><input type="text" name="company_website" tabindex="-1" autocomplete="off"></div>
      <div class="form-row">
        <div class="field"><label>Name *</label><input name="name" required placeholder="Your full name"></div>
        <div class="field"><label>Phone *</label><input name="phone" required placeholder="Mobile number" inputmode="tel"></div>
      </div>
      <div class="field"><label>Email</label><input name="email" type="email" placeholder="you@example.com"></div>
      <div class="form-row">
        <div class="field"><label>Enquiry type</label>
          <select name="enquiry_type">
            <?php foreach (enquiry_types() as $t): ?><option><?= e($t) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="field"><label>Product</label>
          <select name="interested_product" id="qmProduct">
            <option value="">Select a product…</option>
            <?php foreach (products_all() as $p): ?><option><?= e($p['title']) ?></option><?php endforeach; ?>
            <option>Other / Not sure</option>
          </select>
        </div>
      </div>
      <div class="field"><label>Message</label><textarea name="message" placeholder="Quantity, application, city, any questions…"></textarea></div>
      <input type="hidden" name="source" value="quote-modal">
      <button class="btn btn-primary btn-block btn-lg" type="submit" id="qmSubmit"><?= icon('send') ?> Send Enquiry</button>
      <p class="form-note text-center" style="margin:12px 0 0">Or WhatsApp us at <a href="<?= wa_link() ?>" target="_blank" rel="noopener"><?= c('global.phone') ?></a></p>
    </form>
  </div>
</div>

<script>window.MOC_API = "<?= url('api/enquiry.php') ?>";</script>
<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
