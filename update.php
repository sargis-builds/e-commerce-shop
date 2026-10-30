<?php
require 'start.php';
header('Content-Type: application/json');

$id = (int)($_POST['id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 0);

if ($id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Տվյալները բացակայում են']);
    exit;
}

$product = null;
foreach ($products as $category => $items) {
    foreach ($items as $p) {
        if ($p->id === $id) {
            $product = $p;
            break 2;
        }
    }
}

if ($product === null) {
    echo json_encode(['success' => false, 'message' => 'Ապրանքը չկա']);
    exit;
}

$message = $cart->updateQuantity($product, $quantity);
$isSuccess = !str_starts_with($message, "քանակը") 
          && !str_starts_with($message, "Պահեստում") 
          && !str_contains($message, "չկա");

echo json_encode([
    'success' => $isSuccess,
    'message' => $message
]);
exit;