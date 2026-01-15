<?php
session_start();

// 
$host = ''; //localhost
$db   = ''; //il nome del tuo db che vedi da phpmyadmin
$user = ''; //la sua username
$pass = '';//la password brother
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    die('Connessione fallita: '.$e->getMessage());
}

