<?php
/** Brochure download — logs the download then redirects to the PDF. */
require_once __DIR__ . '/includes/functions.php';
$slug = trim($_GET['slug'] ?? '');
$p = $slug ? product_by_slug($slug) : null;
if (!$p || empty($p['brochure_pdf'])) { http_response_code(404); echo 'Brochure not available.'; exit; }
try { q('INSERT INTO downloads (product_slug, ip) VALUES (?,?)', [$p['slug'], $_SERVER['REMOTE_ADDR'] ?? '']); } catch (Throwable $e) {}
$target = $p['brochure_pdf'];
if ($target[0] === '/') $target = base_url() . $target; elseif (!preg_match('#^https?://#', $target)) $target = url($target);
redirect($target);
