<?php
$host = 'localhost';
$db   = 'shop_db';
$user = 'root';     // XAMPP-ի ստանդարտ օգտատերն է
$pass = '';         // XAMPP-ում գաղտնաբառը դատարկ է
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Տվյալները վերադարձնել որպես զանգված (array)
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo "Կապի սխալ: " . $e->getMessage();
}