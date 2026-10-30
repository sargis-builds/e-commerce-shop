<?php
require 'start.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Ստացում և մաքրում
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? ''; 

    // 2. Նորմալիզացիա
    $full_name = mb_convert_case($full_name, MB_CASE_TITLE, "UTF-8");
    $username  = mb_strtolower($username);
    $email     = mb_strtolower($email);

    // 3. Սերվերային ստուգումներ
    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        $error = "Խնդրում եմ լրացնել բոլոր դաշտերը:";
    } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Խնդրում եմ մուտքագրել վավեր էլ. փոստ:";
    }
    elseif (mb_strlen($password) < 6) {
        $error = "Գաղտնաբառը պետք է լինի առնվազն 6 նիշ:";
    }
    elseif (!preg_match('/^[a-z0-9_]{3,20}$/', $username)) {
        $error = "Մուտքանունը պետք է լինի 3-20 նիշ, միայն լատինատառ, թվեր և _ նշան:";
    } else {
        // 4. Բազայի ստուգում (արդյոք զբաղված է)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            $error = "Այս մուտքանունը կամ էլ. փոստը արդեն զբաղված է:";
        } else {
            // 5. ԳՐԱՆՑՈՒՄ (Այստեղ են մեր նոր 3 տողերը)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->execute([$full_name, $username, $email, $hashed_password]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $full_name;

            // 6. Վերահղում մուտքի էջին
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Գրանցում — Իմ Խանութը</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-box">
        
        <h1 class="auth-title">Ստեղծել նոր հաշիվ</h1>

        <?php if (!empty($error)): ?>
            <div class="form-error-general">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="auth-form">
            <label>Անուն Ազգանուն՝</label>
            <input type="text" name="full_name" required>

            <label>Մուտքանուն՝</label>
            <input type="text" name="username" required>

            <label>Էլ. փոստ՝</label>
            <input type="email" name="email" required>

            <label>Գաղտնաբառ՝</label>
            <input type="password" name="password" required>

            <button type="submit" class="auth-submit-btn">
                ✅ Գրանցվել
            </button>
        </form>

        <div class="auth-links">
            Արդեն ունե՞ք հաշիվ: <a href="user-login.php">Մուտք գործել</a>
        </div>

    </div>
</body>
</html>