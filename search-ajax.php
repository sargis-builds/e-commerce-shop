<?php
require 'start.php';

header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');

// Եթե որոնումը դատարկ է կամ 1 նիշից կարճ → դատարկ ցուցակ
if (mb_strlen($q) < 1) {
    echo json_encode(['results' => [], 'count' => 0]);
    exit;
}

$starts   = $q . '%';
$contains = '%' . $q . '%';

$stmt = $pdo->prepare("
    SELECT id, name, description, price, discount, category, stock,
        CASE
            WHEN name LIKE ? THEN 1
            WHEN name LIKE ? THEN 2
            WHEN description LIKE ? THEN 3
            ELSE 4
        END AS relevance
    FROM products
    WHERE name LIKE ? OR description LIKE ?
    ORDER BY relevance ASC, name ASC
    LIMIT 15
");
$stmt->execute([$starts, $contains, $contains, $contains, $contains]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$results = [];
foreach ($rows as $row) {
    $results[] = [
        'id'    => (int)$row['id'],
        'name'  => $row['name'],
        'price' => (int)$row['price'],
        'final' => (int)($row['price'] - $row['discount']),
        'discount' => (int)$row['discount'],
    ];
}

echo json_encode([
    'results' => $results,
    'count'   => count($results),
    'query'   => $q
]);