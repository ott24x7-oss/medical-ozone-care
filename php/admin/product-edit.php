<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
start_session();
require_admin();

$id = (int) ($_GET['id'] ?? 0);
$p = $id ? product_by_id($id) : null;
$isEdit = (bool) $p;

$lines = fn($t) => array_values(array_filter(array_map('trim', explode("\n", (string) $t)), fn($x) => $x !== ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    // parse list fields
    $features = [];
    foreach ($lines($_POST['features'] ?? '') as $l) {
        $parts = array_map('trim', explode('|', $l));
        $features[] = count($parts) >= 3
            ? ['icon' => $parts[0], 'title' => $parts[1], 'text' => implode(' ', array_slice($parts, 2))]
            : ['icon' => 'badge', 'title' => $parts[0], 'text' => $parts[1] ?? ''];
    }
    $specs = [];
    foreach ($lines($_POST['specifications'] ?? '') as $l) { $i = strpos($l, '|'); if ($i !== false) $specs[trim(substr($l, 0, $i))] = trim(substr($l, $i + 1)); }
    $acc = [];
    foreach ($lines($_POST['accessories'] ?? '') as $l) { $parts = array_map('trim', explode('|', $l)); $acc[] = ['name' => $parts[0], 'qty' => $parts[1] ?? '']; }

    // images: kept + typed + uploaded
    $images = array_values(array_filter($_POST['keep_img'] ?? [], fn($x) => trim($x) !== ''));
    $typed = trim($_POST['add_img_path'] ?? '');
    if ($typed !== '') $images[] = $typed;
    if (!empty($_FILES['upload_imgs']) && is_array($_FILES['upload_imgs']['name'])) {
        $allow = ['png' => 1, 'jpg' => 1, 'jpeg' => 1, 'webp' => 1, 'svg' => 1, 'gif' => 1];
        $updir = __DIR__ . '/../uploads';
        if (!is_dir($updir)) @mkdir($updir, 0775, true);
        foreach ($_FILES['upload_imgs']['name'] as $k => $name) {
            if ($_FILES['upload_imgs']['error'][$k] !== UPLOAD_ERR_OK) continue;
            if ($_FILES['upload_imgs']['size'][$k] > 8 * 1024 * 1024) continue;
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!isset($allow[$ext])) continue;
            $fn = slugify(pathinfo($name, PATHINFO_FILENAME)) . '-' . substr(md5(uniqid('', true)), 0, 6) . '.' . $ext;
            if (move_uploaded_file($_FILES['upload_imgs']['tmp_name'][$k], $updir . '/' . $fn)) $images[] = 'uploads/' . $fn;
        }
    }

    $slug = slugify($_POST['slug'] ?? '') ?: slugify($_POST['title'] ?? '');
    $data = [
        'slug' => $slug,
        'title' => trim($_POST['title'] ?? ''),
        'model_number' => trim($_POST['model_number'] ?? ''),
        'category' => trim($_POST['category'] ?? ''),
        'price' => trim($_POST['price'] ?? '') ?: 'On Request',
        'price_value' => ($_POST['price_value'] ?? '') === '' ? null : (int) $_POST['price_value'],
        'featured' => isset($_POST['featured']) ? 1 : 0,
        'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
        'sort_order' => (int) ($_POST['sort_order'] ?? 100),
        'has_concentration_table' => isset($_POST['has_concentration_table']) ? 1 : 0,
        'warranty' => trim($_POST['warranty'] ?? ''),
        'tagline' => trim($_POST['tagline'] ?? ''),
        'short_description' => trim($_POST['short_description'] ?? ''),
        'full_description' => trim($_POST['full_description'] ?? ''),
        'highlights' => json_encode($lines($_POST['highlights'] ?? '')),
        'features' => json_encode($features),
        'specifications' => json_encode($specs),
        'accessories' => json_encode($acc),
        'items' => json_encode($isEdit ? ($p['items'] ?: []) : []),
        'images' => json_encode($images),
        'brochure_pdf' => $isEdit ? ($p['brochure_pdf'] ?? '') : '',
    ];

    $errMsg = '';
    if ($data['title'] === '') $errMsg = 'Title is required.';
    elseif ($data['slug'] === '') $errMsg = 'Could not derive a slug.';
    else {
        $clash = q_one('SELECT id FROM products WHERE slug = ? AND id <> ?', [$data['slug'], $id]);
        if ($clash) $errMsg = 'Another product already uses that slug.';
    }

    if ($errMsg === '') {
        if ($isEdit) {
            $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data))) . ", updated_at = " . (db_driver() === 'sqlite' ? "datetime('now')" : "CURRENT_TIMESTAMP");
            q("UPDATE products SET $set WHERE id = ?", array_merge(array_values($data), [$id]));
            flash('Product updated.');
        } else {
            $cols = implode(',', array_keys($data));
            $ph = implode(',', array_fill(0, count($data), '?'));
            q("INSERT INTO products ($cols) VALUES ($ph)", array_values($data));
            flash('Product created.');
        }
        redirect(url('admin/products.php'));
    }
    // fall through to re-render with $errMsg; repopulate from POST
    $p = array_merge($p ?: [], $data, [
        'highlights' => $lines($_POST['highlights'] ?? ''), 'features' => $features,
        'specifications' => $specs, 'accessories' => $acc, 'images' => $images,
        'model_number' => $data['model_number'],
    ]);
    $isEdit = $id > 0;
}

// prefill helpers
$featLines = '';
foreach (($p['features'] ?? []) as $f) $featLines .= ($f['icon'] ?? 'badge') . ' | ' . ($f['title'] ?? '') . ' | ' . ($f['text'] ?? '') . "\n";
$specLines = '';
foreach (($p['specifications'] ?? []) as $k => $v) $specLines .= "$k | $v\n";
$accLines = '';
foreach (($p['accessories'] ?? []) as $a) $accLines .= ($a['name'] ?? '') . ' | ' . ($a['qty'] ?? '') . "\n";
$hiLines = implode("\n", $p['highlights'] ?? []);
$imgs = $p['images'] ?? [];

$ADMIN_TITLE = $isEdit ? 'Edit product' : 'Add product';
require __DIR__ . '/inc_head.php';
?>
<div class="panel">
  <?php if (!empty($errMsg)): ?><div class="form-msg err" style="display:block"><?= e($errMsg) ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="grid2">
      <div class="fld"><label>Title *</label><input name="title" value="<?= e($p['title'] ?? '') ?>" required></div>
      <div class="fld"><label>Slug (URL)</label><input name="slug" value="<?= e($p['slug'] ?? '') ?>" placeholder="auto from title"></div>
    </div>
    <div class="grid2">
      <div class="fld"><label>Model number</label><input name="model_number" value="<?= e($p['model_number'] ?? '') ?>"></div>
      <div class="fld"><label>Category</label><select name="category"><?php foreach (categories() as $c): ?><option <?= ($p['category'] ?? '') === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select></div>
    </div>
    <div class="grid2">
      <div class="fld"><label>Price (display)</label><input name="price" value="<?= e($p['price'] ?? 'On Request') ?>" placeholder="₹65,000 or On Request"></div>
      <div class="fld"><label>Price value (number)</label><input name="price_value" type="number" value="<?= e($p['price_value'] ?? '') ?>" placeholder="65000"></div>
    </div>
    <div class="grid2">
      <div class="fld"><label>Status</label><select name="status"><option value="active" <?= ($p['status'] ?? 'active') !== 'inactive' ? 'selected' : '' ?>>active</option><option value="inactive" <?= ($p['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>inactive</option></select></div>
      <div class="fld"><label>Sort order</label><input name="sort_order" type="number" value="<?= e($p['sort_order'] ?? $p['sortOrder'] ?? 100) ?>"></div>
    </div>
    <div class="grid2">
      <div class="fld"><label><input type="checkbox" name="featured" <?= !empty($p['featured']) ? 'checked' : '' ?>> Featured</label></div>
      <div class="fld"><label><input type="checkbox" name="has_concentration_table" <?= !empty($p['has_concentration_table']) ? 'checked' : '' ?>> Show concentration chart</label></div>
    </div>
    <div class="fld"><label>Warranty</label><input name="warranty" value="<?= e($p['warranty'] ?? '1 year') ?>"></div>
    <div class="fld"><label>Tagline</label><input name="tagline" value="<?= e($p['tagline'] ?? '') ?>"></div>
    <div class="fld"><label>Short description</label><textarea name="short_description"><?= e($p['short_description'] ?? '') ?></textarea></div>
    <div class="fld"><label>Full description</label><textarea name="full_description"><?= e($p['full_description'] ?? '') ?></textarea></div>
    <div class="fld"><label>Highlights <span class="hint">one per line</span></label><textarea name="highlights"><?= e($hiLines) ?></textarea></div>
    <div class="fld"><label>Features <span class="hint">one per line: icon | title | text</span></label><textarea name="features"><?= e(trim($featLines)) ?></textarea></div>
    <div class="fld"><label>Specifications <span class="hint">one per line: key | value</span></label><textarea name="specifications"><?= e(trim($specLines)) ?></textarea></div>
    <div class="fld"><label>Accessories included <span class="hint">one per line: name | qty</span></label><textarea name="accessories"><?= e(trim($accLines)) ?></textarea></div>
    <div class="fld"><label>Images</label>
      <?php if ($imgs): ?><div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:8px">
        <?php foreach ($imgs as $im): ?>
          <label style="position:relative;width:90px;text-align:center;font-size:.72rem">
            <img src="<?= img_url($im) ?>" style="width:90px;height:64px;object-fit:contain;background:var(--grad-soft);border-radius:8px;border:1px solid var(--line)"><br>
            <input type="checkbox" name="keep_img[]" value="<?= e($im) ?>" checked> keep
          </label>
        <?php endforeach; ?>
      </div><span class="hint">Untick "keep" to remove an image on save.</span><?php endif; ?>
      <div style="margin-top:10px"><label class="hint">Upload image(s)</label><input type="file" name="upload_imgs[]" accept="image/*" multiple></div>
      <div style="margin-top:8px"><label class="hint">…or add an image path</label><input name="add_img_path" placeholder="assets/img/products/device-hero.svg"></div>
    </div>
    <div style="display:flex;gap:10px;margin-top:8px">
      <button class="btn btn-primary" type="submit">Save product</button>
      <a class="btn btn-outline" href="<?= url('admin/products.php') ?>">Cancel</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/inc_foot.php'; ?>
