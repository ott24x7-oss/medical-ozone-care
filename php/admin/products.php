<?php
$ADMIN_TITLE = 'Products';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
start_session();
require_admin();

// delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete' && csrf_check()) {
    q('DELETE FROM products WHERE id = ?', [(int) ($_POST['id'] ?? 0)]);
    flash('Product deleted.');
    redirect(url('admin/products.php'));
}
require __DIR__ . '/inc_head.php';
$rows = q_all('SELECT * FROM products ORDER BY sort_order ASC, id ASC');
?>
<div class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
    <h2 style="margin:0">All products (<?= count($rows) ?>)</h2>
    <a class="btn btn-primary mini" href="<?= url('admin/product-edit.php') ?>">+ Add product</a>
  </div>
  <table class="at"><thead><tr><th></th><th>Title</th><th>Category</th><th>Price</th><th>Status</th><th></th></tr></thead><tbody>
    <?php foreach ($rows as $r): $imgs = json_decode($r['images'], true) ?: []; ?>
      <tr>
        <td><img src="<?= asset(ltrim($imgs[0] ?? 'img/products/device-card.svg', '/')) ?>" alt=""></td>
        <td><b><?= e($r['title']) ?></b><br><span class="who"><?= e($r['model_number']) ?> · /<?= e($r['slug']) ?><?= $r['featured'] ? ' · ★' : '' ?></span></td>
        <td><?= e($r['category']) ?></td><td><?= e($r['price']) ?></td>
        <td><span class="pill <?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
        <td><div style="display:flex;gap:6px">
          <a class="btn btn-outline mini" href="<?= url('admin/product-edit.php?id=' . $r['id']) ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Delete this product?')" style="display:inline">
            <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>">
            <button class="btn btn-outline mini" style="color:var(--danger)" type="submit">Delete</button>
          </form>
        </div></td>
      </tr>
    <?php endforeach; ?>
  </tbody></table>
</div>
<?php require __DIR__ . '/inc_foot.php'; ?>
