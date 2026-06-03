<?php
$ADMIN_TITLE = 'Dashboard';
require_once __DIR__ . '/../includes/functions.php';
require __DIR__ . '/inc_head.php';

$cnt = fn($s) => (int) q_val('SELECT COUNT(*) FROM enquiries WHERE status = ?', [$s]);
$stats = [
    ['Total enquiries', (int) q_val('SELECT COUNT(*) FROM enquiries')],
    ['New', $cnt('New')], ['Contacted', $cnt('Contacted')], ['Quoted', $cnt('Quoted')], ['Closed', $cnt('Closed')],
    ['Products', (int) q_val('SELECT COUNT(*) FROM products')],
    ['Brochure downloads', (int) q_val('SELECT COUNT(*) FROM downloads')],
];
$recent = q_all('SELECT * FROM enquiries ORDER BY created_at DESC, id DESC LIMIT 6');
?>
<div class="a-stats">
  <?php foreach ($stats as $s): ?><div class="a-stat"><div class="n"><?= $s[1] ?></div><div class="l"><?= e($s[0]) ?></div></div><?php endforeach; ?>
</div>
<div class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center"><h2>Recent enquiries</h2><a class="btn btn-outline mini" href="<?= url('admin/enquiries.php') ?>">View all</a></div>
  <?php if ($recent): ?>
  <table class="at"><thead><tr><th>When</th><th>Contact</th><th>Type</th><th>Product</th><th>Status</th></tr></thead><tbody>
    <?php foreach ($recent as $r): ?>
      <tr><td><?= e(substr($r['created_at'], 0, 16)) ?></td>
        <td><b><?= e($r['name']) ?></b><br><span class="who"><?= e($r['phone']) ?></span></td>
        <td><?= e($r['enquiry_type']) ?></td><td><?= e($r['interested_product']) ?: '—' ?></td>
        <td><span class="pill <?= e($r['status']) ?>"><?= e($r['status']) ?></span></td></tr>
    <?php endforeach; ?>
  </tbody></table>
  <?php else: ?><p class="who">No enquiries yet.</p><?php endif; ?>
</div>
<?php require __DIR__ . '/inc_foot.php'; ?>
