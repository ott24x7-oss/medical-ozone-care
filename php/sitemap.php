<?php
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: application/xml; charset=UTF-8');
$base = rtrim(ch('seo.base_url') ?: base_url(), '/');
$urls = ['index.php', 'products.php', 'instructions.php', 'faq.php', 'about.php', 'contact.php',
    'legal.php?doc=terms', 'legal.php?doc=privacy', 'legal.php?doc=disclaimer', 'legal.php?doc=warranty', 'legal.php?doc=usage'];
foreach (products_all() as $p) $urls[] = 'product.php?slug=' . $p['slug'];
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) echo '  <url><loc>' . e($base . '/' . $u) . "</loc></url>\n";
echo "</urlset>\n";
