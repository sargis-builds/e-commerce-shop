<?php
require_once 'admin-auth.php';
require_once 'start.php';

// Ստուգում ենք՝ POST հարցում է, թե ոչ
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin-products.php');
    exit;
}

// Վերցնում ենք բոլոր դաշտերը
$id          = $_POST['id'] ?? null;
$isEdit      = $id !== null && is_numeric($id);

$name        = trim($_POST['name'] ?? '');
$category    = trim($_POST['category'] ?? '');
$price       = $_POST['price'] ?? 0;
$discount    = $_POST['discount'] ?? 0;
$stock       = $_POST['stock'] ?? 0;
$description = trim($_POST['description'] ?? '');

// ─── Նորմալիզացիա ───
$category = mb_convert_case($category, MB_CASE_TITLE, 'UTF-8');
$price    = (int)$price;
$discount = (int)$discount;
$stock    = (int)$stock;

// ─── Ստուգումներ ───
$errors = [];

if ($name === '') {
    $errors[] = 'Անունը պարտադիր է';
}
if ($category === '') {
    $errors[] = 'Կատեգորիան պարտադիր է';
}
if ($price < 0) {
    $errors[] = 'Գինը չի կարող բացասական լինել';
}
if ($discount < 0) {
    $errors[] = 'Զեղչը չի կարող բացասական լինել';
}
if ($discount > $price) {
    $errors[] = 'Զեղչը չի կարող գնից մեծ լինել';
}
if ($stock < 0) {
    $errors[] = 'Քանակը չի կարող բացասական լինել';
}

if (!empty($errors)) {
    // Սխալների դեպքում վերադառնում ենք ձևին
    // (հետո կանենք գեղեցիկ ցուցադրում, հիմա պարզագույն redirect)
    header('Location: admin-product-form.php' . ($isEdit ? '?id=' . $id : ''));
    exit;
}

// ─── INSERT կամ UPDATE ───
if ($isEdit) {
    // Ստուգում ենք, որ այդ id-ն գոյություն ունի
    $check = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $check->execute([$id]);
    if (!$check->fetch()) {
        header('Location: admin-products.php');
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE products
        SET name = ?, category = ?, price = ?, discount = ?, stock = ?, description = ?
        WHERE id = ?
    ");
    $stmt->execute([$name, $category, $price, $discount, $stock, $description, $id]);

} else {
    $stmt = $pdo->prepare("
        INSERT INTO products (name, category, price, discount, stock, description)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $category, $price, $discount, $stock, $description]);
}

// ─── Վերադարձ ցուցակին ───
header('Location: admin-products.php');
exit;