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

$current_url = $_SERVER['REQUEST_URI'];

if (isset($_SESSION['id_user'])) {
    if (strpos($current_url, 'main')) {
        include('main.php');
    } else if (strpos($current_url, 'chat')) {
        include('chat.php');
    } else if (strpos($current_url, 'profile')) {
        include('profile.php');
    }
} else {
    if (strpos($current_url, 'register')) {
        include('register.php');
    } else {
        include('login.php');
    }
}

