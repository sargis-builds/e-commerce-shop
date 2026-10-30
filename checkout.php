<?php
require 'start.php';

if (empty($cart->products)) {
    header('Location: cart.php');
    exit;
}

// Սահմանում ենք փոփոխականները դատարկ (առաջին անգամ բացելիս)
$name = '';
$phone = '';
$address = '';
$errors = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $address = trim($_POST['address'] ?? '');

    // Անուն — միայն տառեր, բացատ, գծիկ, կետ (2+ նիշ)
    if (mb_strlen($name) < 2) {
        $errors['name'] = "Անունը պետք է լինի առնվազն 2 տառ";
    } elseif (!preg_match('/^[\p{L}\s\-\.\']+$/u', $name)) {
        $errors['name'] = "Անունը կարող է պարունակել միայն տառեր";
    }

    // Հեռախոս — միայն թվեր, +, -, space (8-20 նիշ)
    if (!preg_match('/^\+?[0-9\s\-]{8,20}$/', $phone)) {
        $errors['phone'] = "Հեռախոսի ձևաչափը սխալ է (օր.՝ +37411111111)";
    }

    // Հասցե — առնվազն 5 նիշ
    if (mb_strlen($address) < 5) {
        $errors['address'] = "Հասցեն պետք է լինի առնվազն 5 նիշ";
    }

    if (!empty($errors)) {
        $error = "Խնդրում ենք ուղղել նշված դաշտերը";
    } else {
        // 1. Ստեղծում ենք պատվեր՝ դատարկ order_number-ով
        $stmt = $pdo->prepare("
            INSERT INTO orders (order_number, name, phone, address, total, created_at)
            VALUES ('', ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $name,
            $phone,
            $address,
            $cart->getTotal()
        ]);

        // 2. Ստանում ենք MySQL-ի տված id-ն
        $orderId = $pdo->lastInsertId();

        // 3. Պահում ենք session-ում
        $_SESSION['last_order_id'] = $orderId;

        // 4. Սարքում ենք order_number-ը id-ից
        $orderNumber = 'ORD-' . $orderId;

        // 5. Թարմացնում ենք պատվերը
        $stmt = $pdo->prepare("UPDATE orders SET order_number = ? WHERE id = ?");
        $stmt->execute([$orderNumber, $orderId]);

        // 6. Լցնում ենք order_items-ը
        $stmtItem = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, quantity, price, discount)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach ($cart->products as $item) {
            $product  = $item['product'];
            $quantity = $item['quantity'];

            $stmtItem->execute([
                $orderId,
                $product->id,
                $product->name,
                $quantity,
                $product->price,
                $product->discount
            ]);
        }

        // 7. Թարմացնում ենք stock-ը
        $cart->checkout($pdo);

        // 8. Redirect
        header('Location: thanks.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    
    <meta charset="UTF-8">
    <title>Իմ Խանութը</title>
</head>
<body>
 <?php $pageType = 'minimal'; require 'header.php'; ?>

    <a href="cart.php" class="btn-back">← Վերադառնալ Զամբյուղ</a>

<div class="cart-title">
<h1>Ձեր Պատվերը</h1>
</div>

<div class="checkout-summary">
    <h2>Պատվերի ամփոփում</h2>
    <h3>Ցուցակ</h3>
     <p class="old-price"> Արժեքը <?= number_format($cart->getTotalDefault()) ?> Դ</p>
         <p class="saved"> 🎉 Իմ զեղչը՝ -<?= number_format($cart->getTotalDiscount()) ?> Դ</p>
    <h3> Ընդհանուր՝ <?= number_format($cart->getTotal()) ?> Դ</h3>
</div>

  

        <section class="checkout-form">
    <form action="checkout.php" method="POST">
        <h2>Ձեր տվյալները</h2>

        <?php if (!empty($error)): ?>
            <div class="form-error-general"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-field">
            <label for="name">Անուն</label>
            <input type="text" id="name" name="name"
                   placeholder="Սարգիս"
                   value="<?= htmlspecialchars($name ?? '') ?>"
                   class="<?= isset($errors['name']) ? 'input-error' : '' ?>"
                   required>
            <?php if (isset($errors['name'])): ?>
                <p class="field-error"><?= htmlspecialchars($errors['name']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-field">
            <label for="phone">Հեռախոս</label>
            <input type="tel" id="phone" name="phone"
                   placeholder="+37411111111"
                   value="<?= htmlspecialchars($phone ?? '') ?>"
                   class="<?= isset($errors['phone']) ? 'input-error' : '' ?>"
                   required>
            <?php if (isset($errors['phone'])): ?>
                <p class="field-error"><?= htmlspecialchars($errors['phone']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-field">
            <label for="address">Հասցե</label>
            <input type="text" id="address" name="address"
                   placeholder="Երևան, Աբովյան 12"
                   value="<?= htmlspecialchars($address ?? '') ?>"
                   class="<?= isset($errors['address']) ? 'input-error' : '' ?>"
                   required>
            <?php if (isset($errors['address'])): ?>
                <p class="field-error"><?= htmlspecialchars($errors['address']) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-checkout">✓ Հաստատել գնումը</button>
    </form>
</section>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-section">
            <h4>🛒 Իմ Խանութը</h4>
            <p>Որակյալ ապրանքներ, արագ առաքում</p>
        </div>
        <div class="footer-section">
            <h4>Հղումներ</h4>
            <p>Մեր մասին</p>
            <p>Կապ</p>
            <p>Առաքում</p>
        </div>
        <div class="footer-section">
            <h4>Կապ</h4>
            <p>📞 +374 XX XXX XXX</p>
            <p>✉️ info@shop.am</p>
            <p>📍 Երևան, Հայաստան</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2026 Իմ Խանութը։ Բոլոր իրավունքները պաշտպանված են։</p>
    </div>
</footer>

<script src="script.js"></script>
</body>
</html>