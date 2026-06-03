<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
start_session();
require_admin();

// distinct groups in display order
$groups = array_map(fn($r) => $r['cgroup'], q_all('SELECT cgroup, MIN(sort_order) s FROM content GROUP BY cgroup ORDER BY s ASC'));
if (!$groups) $groups = ['Global'];
$group = $_GET['group'] ?? $groups[0];
if (!in_array($group, $groups, true)) $group = $groups[0];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $vals = $_POST['c'] ?? [];
    $badJson = [];
    $upd = db()->prepare('UPDATE content SET cvalue = ? WHERE ckey = ?');
    foreach ($vals as $ckey => $val) {
        $row = q_one('SELECT ctype FROM content WHERE ckey = ?', [$ckey]);
        if (!$row) continue;
        $val = (string) $val;
        if ($row['ctype'] === 'json') {
            $val = trim($val);
            if ($val !== '' && json_decode($val) === null) { $badJson[] = $ckey; }
        }
        $upd->execute([$val, $ckey]);
    }
    flash($badJson ? ('Saved. ⚠ Check JSON for: ' . implode(', ', $badJson)) : 'Content saved.', $badJson ? 'err' : 'ok');
    redirect(url('admin/content.php?group=' . urlencode($_POST['group'] ?? $group)));
}

$ADMIN_TITLE = 'Site Content';
require __DIR__ . '/inc_head.php';
$rows = q_all('SELECT * FROM content WHERE cgroup = ? ORDER BY sort_order ASC', [$group]);
?>
<div class="panel">
  <div class="chips" style="margin-bottom:16px">
    <?php foreach ($groups as $g): $active = $group === $g ? 'background:var(--teal-d);color:#fff;border-color:var(--teal-d)' : ''; ?>
      <a class="chip" href="<?= url('admin/content.php?group=' . urlencode($g)) ?>" style="<?= $active ?>"><?= e($g) ?></a>
    <?php endforeach; ?>
  </div>
  <p class="who" style="margin-top:-4px">Edit any text below and click <b>Save</b>. Changes appear instantly on the website. JSON fields control repeating blocks (features, steps, testimonials, FAQ…).</p>
  <form method="post">
    <?= csrf_field() ?><input type="hidden" name="group" value="<?= e($group) ?>">
    <?php foreach ($rows as $r):
      $name = 'c[' . e($r['ckey']) . ']'; $val = $r['cvalue']; ?>
      <div class="fld">
        <label><?= e($r['clabel'] ?: $r['ckey']) ?> <span class="hint">(<?= e($r['ckey']) ?><?= $r['ctype'] === 'json' ? ' · JSON' : ($r['ctype'] === 'html' ? ' · HTML' : '') ?>)</span></label>
        <?php if ($r['ctype'] === 'text'): ?>
          <input name="<?= $name ?>" value="<?= e($val) ?>">
        <?php else: ?>
          <textarea name="<?= $name ?>" style="<?= in_array($r['ctype'], ['html', 'json']) ? 'min-height:140px;font-family:monospace;font-size:.84rem' : '' ?>"><?= e($val) ?></textarea>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    <div style="position:sticky;bottom:0;background:#fff;padding-top:10px">
      <button class="btn btn-primary" type="submit">Save <?= e($group) ?> content</button>
      <a class="btn btn-outline" href="<?= url('index.php') ?>" target="_blank">Preview site ↗</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/inc_foot.php'; ?>
