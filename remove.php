<?php
require 'start.php';
header('Content-Type: application/json');

$name = $_POST['name'] ?? null;

if ($name === null) {
    echo json_encode(['success' => false, 'message' => 'Տվյալները բացակայում են']);
    exit;
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

$message = $cart->remove($product);
$isSuccess = !str_contains($message, "չկա");

echo json_encode([
    'success' => $isSuccess,
    'message' => $message
]);
exit;