<?php
require 'start.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $username = mb_strtolower($username);

    if (empty($username) || empty($password)) {
        $error = "Մուտքանունը կամ գաղտնաբառը սխալ է";
    } else {
        $stmt = $pdo->prepare("SELECT id, full_name, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];

            header('Location: index.php');
            exit;
        } else {
            $error = "Մուտքանունը կամ գաղտնաբառը սխալ է";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Մուտք — Իմ Խանութը</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-box">

        <h1 class="auth-title">Մուտք գործել</h1>

        <?php if (!empty($error)): ?>
            <div class="form-error-general">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="auth-form">
            <label>Մուտքանուն՝</label>
            <input type="text" name="username" required>

            <label>Գաղտնաբառ՝</label>
            <input type="password" name="password" required>

            <button type="submit" class="auth-submit-btn">
                🔑 Մուտք
            </button>
        </form>

        <div class="auth-links">
            Հաշիվ չունե՞ք: <a href="user-register.php">Գրանցվել</a>
        </div>

    </div>
</body>
</html>