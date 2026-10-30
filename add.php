<?php
require 'start.php';
header('Content-Type: application/json');

// 1. Անվտանգ ստուգում ID-ի համար
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Անվավեր ապրանք']);
    exit;
}

// 2. Անվտանգ ստուգում քանակի համար
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Քանակը պետք է լինի 0-ից մեծ']);
    exit;
}

// 3. Փնտրում ենք ապրանքը
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

// 4. Ավելացնում ենք զամբյուղ և ստուգում պատասխանը
$message = $cart->add($product, $quantity);
$isSuccess = (strpos($message, "Ներողություն") === false);

echo json_encode([
    'success' => $isSuccess,
    'message' => $message
]);
exit;