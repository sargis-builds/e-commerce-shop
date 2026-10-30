<?php
require 'start.php';
header('Content-Type: application/json');

$name = $_POST['name'] ?? null;
$quantity = (int)($_POST['quantity'] ?? 1);

if ($name === null) {
    echo json_encode(['success' => false, 'message' => 'Տվյալները բացակայում են']);
    exit;
}

// Ստուգում ենք, որ quantity-ն դրական թիվ լինի
if ($quantity < 1) {
    $quantity = 1;
}

$product = null;
foreach ($products as $category => $items) {
    foreach ($items as $p) {
        if ($p->name === $name) {
            $product = $p;
            break 2;
        }
    }
}

if ($product === null) {
    echo json_encode(['success' => false, 'message' => 'Ապրանքը չկա']);
    exit;
}

$message = $cart->add($product, $quantity);
$isSuccess = !str_starts_with($message, "Ներողություն");

echo json_encode([
    'success' => $isSuccess,
    'message' => $message
]);
exit;