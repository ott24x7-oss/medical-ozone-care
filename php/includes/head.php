<?php
require_once __DIR__ . '/functions.php';
$PAGE_TITLE = $PAGE_TITLE ?? ch('seo.title');
$PAGE_DESC  = $PAGE_DESC ?? ch('seo.description');
$OG_IMAGE   = $OG_IMAGE ?? img_url(ch('seo.og_image', 'assets/img/products/device-hero.svg'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($PAGE_TITLE) ?></title>
  <meta name="description" content="<?= e($PAGE_DESC) ?>" />
  <meta name="keywords" content="<?= e(ch('seo.keywords')) ?>" />
  <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml" />
  <meta property="og:title" content="<?= e($PAGE_TITLE) ?>" />
  <meta property="og:description" content="<?= e($PAGE_DESC) ?>" />
  <meta property="og:image" content="<?= e($OG_IMAGE) ?>" />
  <meta property="og:type" content="website" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= asset('css/styles.css') ?>" />
  <?= $EXTRA_HEAD ?? '' ?>
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
