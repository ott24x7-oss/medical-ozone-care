<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
start_session();
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $eid = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        q('DELETE FROM enquiries WHERE id = ?', [$eid]);
        flash('Enquiry deleted.');
    } elseif ($action === 'save') {
        $allowed = ['New', 'Contacted', 'Quoted', 'Closed'];
        $status = in_array($_POST['status'] ?? '', $allowed, true) ? $_POST['status'] : 'New';
        $note = trim(mb_substr($_POST['admin_note'] ?? '', 0, 2000));
        q('UPDATE enquiries SET status = ?, admin_note = ? WHERE id = ?', [$status, $note, $eid]);
        flash('Enquiry updated.');
    }
    redirect(url('admin/enquiries.php' . (!empty($_GET['status']) ? '?status=' . urlencode($_GET['status']) : '')));
}

$ADMIN_TITLE = 'Enquiries';
require __DIR__ . '/inc_head.php';

$filter = $_GET['status'] ?? 'All';
$statuses = ['All', 'New', 'Contacted', 'Quoted', 'Closed'];
$sql = 'SELECT * FROM enquiries';
$params = [];
if ($filter !== 'All') { $sql .= ' WHERE status = ?'; $params[] = $filter; }
$sql .= ' ORDER BY created_at DESC, id DESC';
$rows = q_all($sql, $params);
?>
<div class="panel">
  <div class="chips" style="margin-bottom:14px">
    <?php foreach ($statuses as $s): $active = $filter === $s ? 'background:var(--teal-d);color:#fff;border-color:var(--teal-d)' : ''; ?>
      <a class="chip" href="<?= url('admin/enquiries.php' . ($s === 'All' ? '' : '?status=' . urlencode($s))) ?>" style="<?= $active ?>"><?= e($s) ?></a>
    <?php endforeach; ?>
  </div>
  <?php if (!$rows): ?><p class="who">No enquiries<?= $filter !== 'All' ? ' with status ' . e($filter) : '' ?>.</p><?php else: ?>
  <table class="at"><thead><tr><th>When</th><th>Contact</th><th>Type / Product</th><th>Message &amp; note</th><th>Status</th><th></th></tr></thead><tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td style="white-space:nowrap"><?= e(substr($r['created_at'], 0, 16)) ?><br><span class="who"><?= e($r['source']) ?></span></td>
        <td><b><?= e($r['name']) ?></b><br><a href="tel:<?= e($r['phone']) ?>"><?= e($r['phone']) ?></a><?= $r['email'] ? '<br><a href="mailto:' . e($r['email']) . '">' . e($r['email']) . '</a>' : '' ?></td>
        <td><?= e($r['enquiry_type']) ?><br><span class="who"><?= e($r['interested_product']) ?: '—' ?></span></td>
        <td style="max-width:260px">
          <form method="post">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>">
            <div style="margin-bottom:6px"><?= e($r['message']) ?: '—' ?></div>
            <input name="admin_note" value="<?= e($r['admin_note']) ?>" placeholder="admin note…" style="width:100%;padding:6px 8px;border:1px solid var(--line);border-radius:8px;font-size:.82rem;margin-bottom:6px">
            <div style="display:flex;gap:6px;align-items:center">
              <select name="status" style="padding:6px 8px;border:1px solid var(--line);border-radius:8px">
                <?php foreach (['New', 'Contacted', 'Quoted', 'Closed'] as $s): ?><option <?= $r['status'] === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?>
              </select>
              <button class="btn btn-primary mini" name="action" value="save" type="submit">Save</button>
            </div>
        </td>
        <td><span class="pill <?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
        <td><button class="btn btn-outline mini" name="action" value="delete" type="submit" style="color:var(--danger)" onclick="return confirm('Delete this enquiry?')">Del</button></form></td>
      </tr>
    <?php endforeach; ?>
  </tbody></table>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/inc_foot.php'; ?>
