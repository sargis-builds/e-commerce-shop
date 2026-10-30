<?php
require 'start.php';

if (!empty($cart->products)) {
    header('Location: checkout.php');
    exit;
}

$orderId = $_SESSION['last_order_id'] ?? null;

if ($orderId === null) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<?php $pageType = 'minimal'; require 'header.php'; ?>

<link rel="stylesheet" href="style.css">
    
    <meta charset="UTF-8">
    <title>Իմ Խանութը</title>
</head>
<body>
 

<div class="cart-title">
<h1>Շնորհակալություն գնումների համար</h1>
</div>

<div class="thanks-box">
    <div class="thanks-icon">✅</div>
    <h2>Ձեր պատվերը ընդունված է</h2>
    
        <div class="thanks-info">
        <p><strong>Պատվերի համար՝</strong> <?= htmlspecialchars($order['order_number']) ?></p>
        <p><strong>Ամսաթիվ՝</strong> <?= htmlspecialchars($order['created_at']) ?></p>
    </div>
    
    <h3>Ինչ գնվեց՝</h3>
    <div class="thanks-items">
        <?php foreach ($orderItems as $item): ?>
            <div class="thanks-line">
                <span><?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?></span>
                <span><?= number_format(($item['price'] - $item['discount']) * $item['quantity']) ?> Դ</span>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="thanks-total">
        Ընդհանուր՝ <?= number_format($order['total']) ?> Դ
    </div>
    
    <a href="index.php" class="btn-back-to-shop">← Վերադառնալ Գլխավոր էջ</a>
</div>

