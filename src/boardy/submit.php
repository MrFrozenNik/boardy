<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    
    if ($message) {
        $stmt = $pdo->prepare('INSERT INTO posts (title, body, author_id) VALUES (?, ?, ?)');
        $stmt->execute(['Сообщение', $message, $_SESSION['user_id']]);
        
        header('Location: messages.php');
        exit;
    }
}

include 'partials/head.php';
include 'partials/nav.php';
?>
<div class="container">
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="mb-4">Новый пост</h2>
                <form action="submit.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Что у вас нового?</label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Опубликовать</button>
                    <a href="messages.php" class="btn btn-link">Отмена</a>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<?php include 'partials/foot.php'; ?>
