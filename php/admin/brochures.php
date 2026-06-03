<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
start_session();
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $pid = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action === 'clear') {
        q('UPDATE products SET brochure_pdf = ? WHERE id = ?', ['', $pid]);
        flash('Brochure removed.');
    } elseif ($action === 'upload' && !empty($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['pdf']['size'] > 20 * 1024 * 1024) flash('File too large (max 20 MB).', 'err');
        elseif (strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION)) !== 'pdf') flash('Please upload a PDF.', 'err');
        else {
            $updir = __DIR__ . '/../uploads';
            if (!is_dir($updir)) @mkdir($updir, 0775, true);
            $fn = slugify(pathinfo($_FILES['pdf']['name'], PATHINFO_FILENAME)) . '-' . substr(md5(uniqid('', true)), 0, 6) . '.pdf';
            if (move_uploaded_file($_FILES['pdf']['tmp_name'], $updir . '/' . $fn)) {
                q('UPDATE products SET brochure_pdf = ? WHERE id = ?', ['uploads/' . $fn, $pid]);
                flash('Brochure uploaded.');
            } else flash('Upload failed.', 'err');
        }
    }
    redirect(url('admin/brochures.php'));
}

$ADMIN_TITLE = 'Brochures';
require __DIR__ . '/inc_head.php';
$rows = q_all('SELECT * FROM products ORDER BY sort_order ASC');
$counts = [];
foreach (q_all('SELECT product_slug, COUNT(*) n FROM downloads GROUP BY product_slug') as $d) $counts[$d['product_slug']] = $d['n'];
?>
<div class="panel">
  <h2>Brochure manager</h2>
  <p class="who" style="margin-top:-6px">Upload a PDF brochure per product. Visitors download via a tracked link on the product page.</p>
  <table class="at"><thead><tr><th>Product</th><th>Brochure</th><th>Downloads</th><th>Upload / replace</th></tr></thead><tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><b><?= e($r['title']) ?></b><br><span class="who"><?= e($r['model_number']) ?></span></td>
        <td><?= $r['brochure_pdf'] ? '<a href="' . img_url($r['brochure_pdf']) . '" target="_blank">View PDF ↗</a>' : '<span class="who">none</span>' ?></td>
        <td><?= (int) ($counts[$r['slug']] ?? 0) ?></td>
        <td>
          <form method="post" enctype="multipart/form-data" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>">
            <input type="file" name="pdf" accept="application/pdf" required style="font-size:.82rem">
            <button class="btn btn-primary mini" name="action" value="upload" type="submit">Upload</button>
            <?php if ($r['brochure_pdf']): ?><button class="btn btn-outline mini" name="action" value="clear" type="submit" onclick="return confirm('Remove brochure?')">Remove</button><?php endif; ?>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody></table>
</div>
<?php require __DIR__ . '/inc_foot.php'; ?>
