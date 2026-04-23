<?php
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name && $email && $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, password) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $hash, 'active']);
            
            $userId = $pdo->lastInsertId();

            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;

            header('Location: messages.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'Этот email уже зарегистрирован.';
            } else {
                $error = 'Ошибка при регистрации: ' . $e->getMessage();
            }
        }
    } else {
        $error = 'Заполните все поля!';
    }
}

include 'partials/head.php';
include 'partials/nav.php';
?>
<div class="container">
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="card-title text-center mb-4">Регистрация</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="register.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Ваше имя</label>
                        <input type="text" name="name" class="form-control" placeholder="Иван Иванов" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email (логин)</label>
                        <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Пароль</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">Зарегистрироваться</button>
                </form>
                
                <div class="text-center mt-3">
                    <small class="text-muted">Уже есть аккаунт? <a href="login.php">Войти</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php include 'partials/foot.php'; ?>
