<?php
/**
 * Medical Ozone Care — one-time installer.
 * 1) Edit config.php with your Hostinger MySQL details.
 * 2) Open this file in a browser:  https://your-domain/install.php
 * 3) After "Installation complete", DELETE this file (or it stays harmless but unneeded).
 *
 * Re-run with ?force=1 to WIPE and reseed (deletes all products/enquiries/content).
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
require __DIR__ . '/includes/db.php';

$force = isset($_GET['force']) && $_GET['force'] == '1';
$driver = db_driver();
$mysql = ($driver !== 'sqlite');
$steps = [];
$err = null;

function out_page($title, $bodyHtml)
{
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>' . htmlspecialchars($title) . '</title><style>'
        . 'body{font-family:Inter,Segoe UI,Arial,sans-serif;background:#f7fbff;color:#102033;margin:0;padding:40px 16px}'
        . '.card{max-width:680px;margin:0 auto;background:#fff;border:1px solid #e3edf1;border-radius:18px;box-shadow:0 14px 40px rgba(6,50,70,.1);padding:34px}'
        . 'h1{margin:0 0 6px;color:#008b7a;font-size:1.6rem}h2{font-size:1.05rem;margin:22px 0 8px}'
        . 'a{color:#1f6feb}code{background:#eef4f7;padding:2px 6px;border-radius:6px}'
        . '.ok{color:#1f9d57}.bad{color:#d0463b}ul{line-height:1.8}.btn{display:inline-block;margin-top:8px;margin-right:8px;background:linear-gradient(135deg,#008b7a,#0db7a5);color:#fff;text-decoration:none;padding:12px 20px;border-radius:12px;font-weight:700}'
        . '.warn{background:#fff8e8;border:1px solid #f6e2b6;color:#6d4700;padding:14px 16px;border-radius:12px;margin-top:16px}'
        . '</style></head><body><div class="card">' . $bodyHtml . '</div></body></html>';
}

// ---- connect ----
try {
    $pdo = db();
} catch (Throwable $e) {
    out_page('Install — DB error', '<h1 class="bad">Database connection failed</h1>'
        . '<p>Could not connect using the details in <code>config.php</code>.</p>'
        . '<p><b>Error:</b> ' . htmlspecialchars($e->getMessage()) . '</p>'
        . '<h2>Fix</h2><ul>'
        . '<li>In Hostinger hPanel → <b>Databases → MySQL Databases</b>, create a database &amp; user.</li>'
        . '<li>Open <code>config.php</code> and set <code>name</code>, <code>user</code>, <code>pass</code> (host is usually <code>localhost</code>).</li>'
        . '<li>Reload this page.</li></ul>');
    exit;
}

// ---- already installed? ----
$installed = false;
try { $installed = (int) q_val('SELECT COUNT(*) FROM products') > 0; } catch (Throwable $e) { $installed = false; }
if ($installed && !$force) {
    out_page('Already installed', '<h1>Already installed ✅</h1>'
        . '<p>Your database already has data, so installation was skipped (nothing was changed).</p>'
        . '<a class="btn" href="index.php">Open website</a><a class="btn" href="admin/login.php">Open admin</a>'
        . '<div class="warn">To start over and <b>erase all data</b> (products, enquiries, content), run '
        . '<a href="install.php?force=1">install.php?force=1</a>.<br>For security, delete <code>install.php</code> after setup.</div>');
    exit;
}

// ---- schema ----
$pk = $mysql ? 'INT AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
$ts = $mysql ? "TIMESTAMP DEFAULT CURRENT_TIMESTAMP" : "TEXT DEFAULT (datetime('now'))";
$txt = 'TEXT';
$eng = $mysql ? ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4' : '';

try {
    if ($force) {
        foreach (['downloads', 'enquiries', 'content', 'admins', 'products'] as $t) {
            $pdo->exec("DROP TABLE IF EXISTS $t");
        }
        $steps[] = 'Dropped existing tables (force).';
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id $pk,
        slug VARCHAR(150) UNIQUE NOT NULL, title VARCHAR(200) NOT NULL, model_number VARCHAR(150),
        category VARCHAR(80), price VARCHAR(40), price_value INT NULL, featured INT DEFAULT 0,
        status VARCHAR(20) DEFAULT 'active', sort_order INT DEFAULT 100, has_concentration_table INT DEFAULT 0,
        warranty VARCHAR(60), tagline VARCHAR(255), short_description $txt, full_description $txt,
        highlights $txt, features $txt, specifications $txt, accessories $txt, items $txt, images $txt,
        brochure_pdf VARCHAR(255), created_at $ts, updated_at $ts
    )$eng");

    $pdo->exec("CREATE TABLE IF NOT EXISTS enquiries (
        id $pk, name VARCHAR(160) NOT NULL, phone VARCHAR(40) NOT NULL, email VARCHAR(160),
        interested_product VARCHAR(200), enquiry_type VARCHAR(60) DEFAULT 'Product Information',
        message $txt, status VARCHAR(20) DEFAULT 'New', admin_note $txt, source VARCHAR(40) DEFAULT 'website',
        created_at $ts
    )$eng");

    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id $pk, email VARCHAR(160) UNIQUE NOT NULL, password_hash VARCHAR(255) NOT NULL,
        name VARCHAR(120), created_at $ts
    )$eng");

    $pdo->exec("CREATE TABLE IF NOT EXISTS content (
        id $pk, ckey VARCHAR(120) UNIQUE NOT NULL, cvalue $txt, cgroup VARCHAR(60),
        clabel VARCHAR(160), ctype VARCHAR(20) DEFAULT 'text', sort_order INT DEFAULT 100
    )$eng");

    $pdo->exec("CREATE TABLE IF NOT EXISTS downloads (
        id $pk, product_slug VARCHAR(150), ip VARCHAR(60), created_at $ts
    )$eng");

    $steps[] = 'Created tables: products, enquiries, admins, content, downloads.';

    // ---- seed ----
    $seed = require __DIR__ . '/includes/seed_data.php';

    // products
    $pcount = 0;
    $pins = $pdo->prepare("INSERT INTO products
        (slug,title,model_number,category,price,price_value,featured,status,sort_order,has_concentration_table,
         warranty,tagline,short_description,full_description,highlights,features,specifications,accessories,items,images,brochure_pdf)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    foreach ($seed['products'] as $p) {
        try {
            $pins->execute([
                $p['slug'], $p['title'], $p['model_number'], $p['category'], $p['price'], $p['price_value'],
                $p['featured'], $p['status'], $p['sort_order'], $p['has_concentration_table'], $p['warranty'],
                $p['tagline'], $p['short_description'], $p['full_description'],
                json_encode($p['highlights']), json_encode($p['features']), json_encode($p['specifications']),
                json_encode($p['accessories']), json_encode($p['items']), json_encode($p['images']), $p['brochure_pdf'],
            ]);
            $pcount++;
        } catch (Throwable $e) { /* skip dupes */ }
    }
    $steps[] = "Seeded $pcount products.";

    // content
    $ccount = 0; $i = 0;
    $cins = $pdo->prepare("INSERT INTO content (ckey,cvalue,cgroup,clabel,ctype,sort_order) VALUES (?,?,?,?,?,?)");
    foreach ($seed['content'] as $row) {
        [$ckey, $cgroup, $clabel, $ctype, $cval] = $row;
        if ($ctype === 'json' && is_array($cval)) $cval = json_encode($cval, JSON_UNESCAPED_UNICODE);
        try { $cins->execute([$ckey, $cval, $cgroup, $clabel, $ctype, $i++]); $ccount++; }
        catch (Throwable $e) { /* skip dupes */ }
    }
    $steps[] = "Seeded $ccount editable content entries.";

    // admin
    $adminCfg = cfg('admin');
    $email = strtolower(trim($adminCfg['email']));
    $exists = q_one('SELECT id FROM admins WHERE email = ?', [$email]);
    if (!$exists) {
        q('INSERT INTO admins (email,password_hash,name) VALUES (?,?,?)',
            [$email, password_hash($adminCfg['password'], PASSWORD_DEFAULT), 'Medical Ozone Care Admin']);
        $steps[] = "Created admin: $email";
    } else {
        $steps[] = "Admin already exists: $email";
    }
} catch (Throwable $e) {
    out_page('Install — error', '<h1 class="bad">Installation error</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>');
    exit;
}

// ---- done ----
$stepHtml = '<ul>';
foreach ($steps as $s) $stepHtml .= '<li class="ok">' . htmlspecialchars($s) . '</li>';
$stepHtml .= '</ul>';
out_page('Installation complete', '<h1>Installation complete 🎉</h1>'
    . '<p>Your Medical Ozone Care website is ready.</p>' . $stepHtml
    . '<a class="btn" href="index.php">Open website</a><a class="btn" href="admin/login.php">Open admin panel</a>'
    . '<h2>Admin login</h2><ul><li>Email: <code>' . htmlspecialchars(cfg('admin')['email']) . '</code></li>'
    . '<li>Password: the one in <code>config.php</code> (change it after logging in)</li></ul>'
    . '<div class="warn"><b>Important:</b> delete <code>install.php</code> now for security. '
    . 'Edit any text on the site from <b>Admin → Content</b>.</div>');
