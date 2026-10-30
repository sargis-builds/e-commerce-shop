<?php
require_once 'admin-auth.php';
require_once 'start.php';

// 1. Ստանում ենք id-ն URL-ից
$productId = $_GET['id'] ?? null;

// 2. Ստուգում ենք, որ id-ն կա և թիվ է (Անվտանգություն)
if ($productId === null || !is_numeric($productId)) {
    header('Location: admin-products.php');
    exit;
}

// 3. Ստուգում ենք, որ այդ id-ով ապրանք իրոք գոյություն ունի
$check = $pdo->prepare("SELECT id FROM products WHERE id = ?");
$check->execute([$productId]);
if (!$check->fetch()) {
    // Եթե չկա, վերադառնում ենք ցանկին
    header('Location: admin-products.php');
    exit;
}

// 4. Ջնջում ենք ապրանքը DB-ից
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$productId]);

// 5. Վերադառնում ենք ցուցակին
header('Location: admin-products.php');
exit;