<?php
$host = 'localhost';
$dbname = 'quiz_app';
$db_username = 'root'; // Замените на вашего пользователя MySQL
$db_password = 'Ivasik2006'; // Замените на ваш пароль MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

session_start();
?>