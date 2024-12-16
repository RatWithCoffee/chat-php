<?php
$servername = 'db';
$username = 'rat';
$password = 'rat';
$dbname = 'db';
$port = '3306';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

session_start();


// Определяем доступные маршруты
$routes = [
    'main' => 'main.php',
    'chat' => 'chat.php',
    'profile' => 'profile.php',
    'register' => 'register.php',
    'login' => 'login.php',
];

$current_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Проверяем, авторизован ли пользователь
if (isset($_SESSION['id_user'])) {
    // Авторизованный пользователь
    if (strpos($current_url, 'main') !== false) {
        include($routes['main']);
    } elseif (strpos($current_url, 'chat') !== false) {
        include($routes['chat']);
    } elseif (strpos($current_url, 'profile') !== false) {
        include($routes['profile']);
    } else {
        // Если ни один маршрут не найден, по умолчанию загружаем главную страницу
        include($routes['main']);
    }
} else {
    // Гость
    if (strpos($current_url, 'register') !== false) {
        include($routes['register']);
    } else {
        // Если ни один маршрут не найден, по умолчанию загружаем страницу входа
        include($routes['login']);
    }
}
?>
