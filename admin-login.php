<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Լրացրու բոլոր դաշտերը';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: admin.php');
            exit;
        } else {
            $error = 'Սխալ մուտքանուն կամ գաղտնաբառ';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <title>Ադմին մուտք</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-login-page">
    <div class="admin-login-box">
        <h1 class="admin-login-title">Ադմին մուտք</h1>

        <?php if ($error !== ''): ?>
            <p class="admin-login-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" class="admin-login-form">
            <label>Մուտքանուն</label>
            <input type="text" name="username" autofocus>

            <label>Գաղտնաբառ</label>
            <input type="password" name="password">

            <button type="submit">Մուտք</button>
        </form>
    </div>
</body>
</html>