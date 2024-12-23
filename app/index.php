<?php

session_start();

// Проверяем, авторизован ли пользователь
if (isset($_SESSION['id_user'])) {
    // Авторизованный пользователь
    include("main.php");
} else {
    include("login.php");
}
