<?php
require_once 'db.php';

$stmt = $pdo->query(
    'SELECT posts.body, users.name, posts.created_at
     FROM posts
     JOIN users ON posts.author_id = users.id
     ORDER BY posts.created_at DESC'
);
$messages = $stmt->fetchAll();

include 'partials/head.php';
include 'partials/nav.php';
?>
<div class="container">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Все сообщения</h2>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/submit.php" class="btn btn-primary">Написать пост</a>
    <?php endif; ?>
</div>

<?php if (empty($messages)): ?>
    <div class="alert alert-info">Сообщений пока нет. Будьте первым!</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover bg-white shadow-sm rounded">
            <thead class="table-light">
                <tr>
                    <th style="width: 20%">Дата</th>
                    <th style="width: 20%">Автор</th>
                    <th>Сообщение</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                <tr>
                    <td class="text-muted small">
                        <?= htmlspecialchars($msg['created_at']) ?>
                    </td>
                    <td>
                        <span class="badge bg-secondary"><?= htmlspecialchars($msg['name']) ?></span>
                    </td>
                    <td><?= htmlspecialchars($msg['body']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
</div>
<?php include 'partials/foot.php'; ?>
