<?php
// backend/config/db_connection.php 

$host = 'b3ehoylez0wwlhvuad4s-mysql.services.clever-cloud.com';
$db   = 'b3ehoylez0wwlhvuad4s';
$user = 'uxdjsyjzrb9m1cpn';
$pass = 'aZETS61JtGBZdLz7jrMG';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

