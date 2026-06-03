<?php
require_once __DIR__ . '/db.php';

/* ---------------- Content (all editable text) ---------------- */
function all_content()
{
    static $cache = null;
    if ($cache !== null) return $cache;
    $cache = [];
    try {
        foreach (q_all('SELECT ckey, cvalue FROM content') as $r) {
            $cache[$r['ckey']] = $r['cvalue'];
        }
    } catch (Throwable $e) { /* table may not exist before install */ }
    return $cache;
}
/** escaped text content */
function c($key, $fallback = '')
{
    $all = all_content();
    $v = $all[$key] ?? null;
    return htmlspecialchars($v !== null && $v !== '' ? $v : $fallback, ENT_QUOTES, 'UTF-8');
}
/** raw content (for HTML blocks like legal pages) */
function ch($key, $fallback = '')
{
    $all = all_content();
    $v = $all[$key] ?? null;
    return $v !== null && $v !== '' ? $v : $fallback;
}
/** JSON content -> array */
function cj($key, $fallback = [])
{
    $all = all_content();
    if (!isset($all[$key]) || $all[$key] === '') return $fallback;
    $d = json_decode($all[$key], true);
    return is_array($d) ? $d : $fallback;
}

/* ---------------- escaping / urls ---------------- */
function e($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }

function base_url()
{
    $set = cfg('site')['base_url'] ?? '';
    if ($set) return rtrim($set, '/');
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') == 443;
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // base path = directory containing the front controller (handles subfolder installs)
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $dir = rtrim($dir, '/');
    // if running inside /admin, strip it for the public base
    if (substr($dir, -6) === '/admin') $dir = substr($dir, 0, -6);
    return $scheme . '://' . $host . $dir;
}
function url($path = '') { return base_url() . '/' . ltrim($path, '/'); }
function asset($path) { return url('assets/' . ltrim($path, '/')); }
/** Resolve a stored image/file path (e.g. "assets/img/.." or "uploads/..") to a full URL. */
function img_url($path)
{
    $path = (string) $path;
    if ($path === '') return asset('img/products/device-card.svg');
    if (preg_match('#^https?://#', $path)) return $path;
    return url(ltrim($path, '/'));
}
function current_file() { return basename($_SERVER['SCRIPT_NAME'] ?? ''); }

function redirect($to) { header('Location: ' . $to); exit; }

/* ---------------- contact / whatsapp ---------------- */
function wa_number() { return preg_replace('/\D/', '', c('global.whatsapp', '919958803980')); }
function wa_link($product = '')
{
    $msg = "Hello Medical Ozone Care,\nI am interested in " . ($product ?: 'your medical ozone equipment') .
        ".\nName:\nLocation:\nRequirement:\nPlease share quotation and details.";
    return 'https://wa.me/' . wa_number() . '?text=' . rawurlencode($msg);
}
function tel_link() { return 'tel:+' . wa_number(); }

/* ---------------- lookups ---------------- */
function categories()
{
    return ['Medical Ozone Generator', 'Oxygen Regulator', 'Accessories', 'Spare Parts'];
}
function enquiry_types()
{
    return ['Quote Request', 'Product Information', 'Distributor Enquiry', 'Service/Support', 'Accessories'];
}

/* ---------------- products ---------------- */
function product_decode($r)
{
    if (!$r) return null;
    foreach (['highlights', 'features', 'specifications', 'accessories', 'items', 'images'] as $f) {
        $r[$f] = json_decode($r[$f] ?? '', true);
        if ($r[$f] === null) $r[$f] = ($f === 'specifications') ? [] : [];
    }
    $r['featured'] = (int) $r['featured'];
    $r['has_concentration_table'] = (int) $r['has_concentration_table'];
    $r['image'] = $r['images'][0] ?? 'assets/img/products/device-card.svg';
    return $r;
}
function products_all($opts = [])
{
    $sql = 'SELECT * FROM products';
    $where = [];
    $params = [];
    if (($opts['status'] ?? 'active') !== 'all') { $where[] = 'status = ?'; $params[] = $opts['status'] ?? 'active'; }
    if (!empty($opts['category'])) { $where[] = 'category = ?'; $params[] = $opts['category']; }
    if (!empty($opts['featured'])) { $where[] = 'featured = 1'; }
    if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
    $sql .= ' ORDER BY sort_order ASC, id ASC';
    return array_map('product_decode', q_all($sql, $params));
}
function product_by_slug($slug) { return product_decode(q_one('SELECT * FROM products WHERE slug = ?', [$slug])); }
function product_by_id($id) { return product_decode(q_one('SELECT * FROM products WHERE id = ?', [$id])); }

function slugify($s)
{
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-');
}

/* ---------------- view helpers ---------------- */
function product_card_html($p)
{
    $img = e(img_url($p['image']));
    $tag = $p['featured'] ? '<span class="tag feat">Flagship</span>' : '<span class="tag">' . e(explode(' ', $p['category'])[0]) . '</span>';
    $price = $p['price_value']
        ? '<span class="price">' . e($p['price']) . '</span>'
        : '<span class="price" style="font-size:.95rem;color:var(--muted)">' . e($p['price']) . '</span>';
    return '<a class="pcard reveal" href="' . url('product.php?slug=' . urlencode($p['slug'])) . '">'
        . '<div class="media">' . $tag . '<img src="' . $img . '" alt="' . e($p['title']) . '" loading="lazy"></div>'
        . '<div class="body"><span class="cat">' . e($p['category']) . '</span>'
        . '<h3>' . e($p['title']) . '</h3>'
        . '<div class="model">' . e($p['model_number']) . ' · <span class="status-dot">In stock</span></div>'
        . '<p>' . e($p['short_description']) . '</p>'
        . '<div class="foot">' . $price . '<span class="btn btn-outline" style="padding:8px 16px">Details ' . icon('arrow') . '</span></div>'
        . '</div></a>';
}

function concentration_table_html()
{
    $t = concentration_table();
    $head = '';
    foreach ($t['levels'] as $i => $l) $head .= '<th class="' . ($i < 4 ? 'm1' : 'm2') . '">' . e($l) . '</th>';
    $rows = '';
    foreach ($t['rows'] as $r) {
        $rows .= '<tr><th>' . e($r[0]) . '</th>';
        foreach ($r[1] as $v) $rows .= '<td>' . ($v === null ? '—' : e($v)) . '</td>';
        $rows .= '</tr>';
    }
    return '<div class="ctable-scroll"><table class="ctable"><thead>'
        . '<tr><th rowspan="2">O₂ Flow</th><th class="m1" colspan="4">Mode M1</th><th class="m2" colspan="2">Mode M2</th></tr>'
        . '<tr>' . $head . '</tr></thead><tbody>' . $rows . '</tbody></table></div>'
        . '<p class="form-note" style="margin-top:12px">All values in µg/ml (mg/L). "—" = not available at that flow.</p>';
}

/* ---------------- csrf ---------------- */
function csrf_token()
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function csrf_check()
{
    return isset($_POST['csrf']) && hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']);
}

/* ---------------- concentration table (AOT-MD-520 reference) ---------------- */
function concentration_table()
{
    return [
        'levels' => ['L1', 'L2', 'L3', 'L4', 'L5', 'L6'],
        'rows' => [
            ['1 L/min', [4.4, 5.3, 6.1, 6.9, 12.6, 15.8]],
            ['3/4 L/min', [5.7, 6.7, 7.8, 8.7, 15.9, 21.6]],
            ['1/2 L/min', [8.9, 10.4, 11.9, 13.2, 22.0, 37.3]],
            ['1/4 L/min', [16.5, 19.2, 21.8, 24.1, 39.7, 51.5]],
            ['1/8 L/min', [26.2, 30.2, 33.9, 37.2, 62.8, 72.8]],
            ['1/16 L/min', [38.4, 43.9, 48.5, 52.0, 82.0, 89.9]],
            ['1/32 L/min', [null, null, null, null, 86.0, 92.5]],
        ],
    ];
}

/* ---------------- icons (SVG) ---------------- */
function icon($name, $fill = false)
{
    static $P = null;
    if ($P === null) $P = [
        'shield' => '<path d="M12 2l8 3v6c0 5-3.5 8.5-8 11-4.5-2.5-8-6-8-11V5l8-3z"/>',
        'sliders' => '<path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/>',
        'atom' => '<circle cx="12" cy="12" r="1.6"/><ellipse cx="12" cy="12" rx="10" ry="4.5"/><ellipse cx="12" cy="12" rx="10" ry="4.5" transform="rotate(60 12 12)"/><ellipse cx="12" cy="12" rx="10" ry="4.5" transform="rotate(120 12 12)"/>',
        'globe' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2c3 3 3 17 0 20M12 2c-3 3-3 17 0 20"/>',
        'plug' => '<path d="M9 2v6M15 2v6M6 8h12v3a6 6 0 0 1-12 0V8zM12 17v5"/>',
        'badge' => '<circle cx="12" cy="8" r="6"/><path d="M8.5 13.5L7 22l5-3 5 3-1.5-8.5"/>',
        'gauge' => '<path d="M12 14l4-4M4.5 19a9 9 0 1 1 15 0"/><circle cx="12" cy="14" r="1"/>',
        'check' => '<path d="M20 6L9 17l-5-5"/>',
        'checkc' => '<circle cx="12" cy="12" r="10"/><path d="M8 12l3 3 5-5"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.6A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
        'mail' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 7l-10 6L2 7"/>',
        'pin' => '<path d="M21 10c0 6-9 12-9 12s-9-6-9-12a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
        'wa' => '<path d="M17.5 14.4c-.3-.2-1.7-.8-2-.9-.3-.1-.5-.2-.7.2-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-1.6-.8-2.7-1.5-3.7-3.3-.3-.5.3-.5.8-1.5.1-.2 0-.4 0-.5 0-.2-.7-1.6-.9-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.2.2 2.1 3.3 5.2 4.6 1.9.8 2.6.9 3.6.7.6-.1 1.7-.7 1.9-1.4.2-.7.2-1.2.2-1.4-.1-.1-.3-.2-.6-.4z"/><path d="M12 2a10 10 0 0 0-8.5 15.2L2 22l4.9-1.4A10 10 0 1 0 12 2z" fill="none"/>',
        'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'award' => '<circle cx="12" cy="8" r="6"/><path d="M15.5 13.5L17 22l-5-3-5 3 1.5-8.5"/>',
        'droplet' => '<path d="M12 2.7l5.7 5.7a8 8 0 1 1-11.4 0z"/>',
        'wind' => '<path d="M3 8h10a2.5 2.5 0 1 0-2.5-2.5M3 16h14a2.5 2.5 0 1 1-2.5 2.5M3 12h18"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.6 1.6 0 0 0-2.7 1.1V21a2 2 0 1 1-4 0v-.1A1.6 1.6 0 0 0 7 19.4a1.6 1.6 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.6 1.6 0 0 0-1.1-2.7H1a2 2 0 1 1 0-4h.1A1.6 1.6 0 0 0 2.6 7a1.6 1.6 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.6 1.6 0 0 0 1.8.3H7a1.6 1.6 0 0 0 1-1.5V1a2 2 0 1 1 4 0v.1a1.6 1.6 0 0 0 2.7 1.1 1.6 1.6 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.6 1.6 0 0 0-.3 1.8V7a1.6 1.6 0 0 0 1.5 1H23a2 2 0 1 1 0 4h-.1a1.6 1.6 0 0 0-1.5 1z"/>',
        'send' => '<path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>',
        'doc' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h8M8 9h2"/>',
        'zap' => '<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>',
        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
        'truck' => '<rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7zM5.5 18.5a2 2 0 1 0 0 .01M18.5 18.5a2 2 0 1 0 0 .01"/>',
        'headset' => '<path d="M4 14v-2a8 8 0 0 1 16 0v2"/><path d="M4 14a2 2 0 0 0 2 2h1v-5H6a2 2 0 0 0-2 2zM20 14a2 2 0 0 1-2 2h-1v-5h1a2 2 0 0 1 2 2zM18 16v1a3 3 0 0 1-3 3h-3"/>',
        'flask' => '<path d="M9 2h6M10 2v6L5 19a1.5 1.5 0 0 0 1.4 2h11.2A1.5 1.5 0 0 0 19 19l-5-11V2"/><path d="M7.5 14h9"/>',
        'star' => '<path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/>',
        'x' => '<path d="M18 6L6 18M6 6l12 12"/>',
    ];
    $p = $P[$name] ?? '';
    if ($fill) {
        return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">' . $p . '</svg>';
    }
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $p . '</svg>';
}
