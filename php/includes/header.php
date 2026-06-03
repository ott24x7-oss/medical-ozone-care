<?php
$cur = current_file();
$nav = [
    ['index.php', c('global.nav_home')],
    ['products.php', c('global.nav_products')],
    ['instructions.php', c('global.nav_instructions')],
    ['faq.php', c('global.nav_faq')],
    ['about.php', c('global.nav_about')],
    ['contact.php', c('global.nav_contact')],
];
?>
<!-- Top info bar (scrolls away) -->
<div class="topbar">
  <div class="container topbar-in">
    <span class="tb-tag"><?= c('global.topbar') ?></span>
    <span class="tb-contact"><a href="<?= tel_link() ?>">Call: <?= c('global.phone') ?></a> <span class="tb-email">· <a href="mailto:<?= c('global.email2') ?>"><?= c('global.email2') ?></a></span></span>
  </div>
</div>

<!-- Sticky nav -->
<nav class="nav" id="nav">
  <div class="container nav-inner">
    <a class="brand" href="<?= url('index.php') ?>"><img src="<?= asset('img/logo.svg') ?>" alt="<?= c('global.brand') ?>" width="180" height="47"></a>
    <ul class="nav-links">
      <?php foreach ($nav as [$href, $label]): ?>
        <li><a href="<?= url($href) ?>" class="<?= $cur === $href ? 'active' : '' ?>"><?= $label ?></a></li>
      <?php endforeach; ?>
    </ul>
    <div class="nav-cta">
      <a href="<?= url('contact.php') ?>" class="btn btn-primary nav-quote" data-quote><?= c('global.cta_quote') ?></a>
      <button class="nav-toggle" id="navToggle" aria-label="Open menu" aria-expanded="false"><span></span><span></span><span></span></button>
    </div>
  </div>
</nav>

<!-- Mobile drawer -->
<div class="mdrawer" id="mdrawer" aria-hidden="true">
  <div class="mdrawer-backdrop" data-mclose></div>
  <aside class="mdrawer-panel" role="dialog" aria-modal="true" aria-label="Menu">
    <div class="mdrawer-head">
      <img class="mdrawer-logo" src="<?= asset('img/logo.svg') ?>" alt="<?= c('global.brand') ?>">
      <button class="mdrawer-close" data-mclose aria-label="Close menu"><?= icon('x') ?></button>
    </div>
    <nav class="mdrawer-links">
      <?php foreach ($nav as [$href, $label]): ?>
        <a href="<?= url($href) ?>" class="<?= $cur === $href ? 'active' : '' ?>"><?= $label ?> <?= icon('arrow') ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="mdrawer-cta">
      <a class="btn btn-primary btn-block" data-quote data-mclose><?= icon('send') ?> <?= c('global.cta_quote') ?></a>
      <a class="btn btn-wa btn-block" href="<?= wa_link() ?>" target="_blank" rel="noopener"><?= icon('wa', true) ?> WhatsApp</a>
      <a class="btn btn-outline btn-block" href="<?= tel_link() ?>"><?= icon('phone') ?> <?= c('global.phone') ?></a>
    </div>
    <div class="mdrawer-info">
      <p><?= icon('mail') ?> <a href="mailto:<?= c('global.email2') ?>"><?= c('global.email2') ?></a></p>
      <p><?= icon('pin') ?> <span><?= c('global.address') ?></span></p>
      <p><?= icon('clock') ?> <span><?= c('global.hours') ?></span></p>
    </div>
  </aside>
</div>
