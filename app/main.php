<?php
include("utils/start_settings.php");
include('header.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Инициализируем сессию только если она еще не начата
}
if (!isset($_SESSION['id_user'])) {
    echo "Пользователь не авторизован.";
    exit();
}

// include("utils/get_user_info.php");


// Получаем ID текущего пользователя
$currentUserId = $_SESSION['id_user'];
// Получаем информацию о пользователе
$sql_user = "SELECT u.id, u.surname, u.name, u.patronymic, u.email, u.birthday, u.login, u.registration, u.avatar, c.name AS city_name
             FROM users u
             LEFT JOIN city c ON u.city_id = c.id
             WHERE u.id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $currentUserId);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    // Получаем данные пользователя
    $user = $result_user->fetch_assoc();
} else {
    echo "Пользователь не найден.";
    exit();
}


// SQL-запрос для получения всех пользователей, кроме текущего
$sql = "SELECT id, surname, name, patronymic, email, birthday, login, avatar, city_id 
        FROM users 
        WHERE id != ?";

// Подготовка и выполнение запроса
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат</title>
    <link rel="stylesheet" href="/static/css/styles.css">
    <link rel="stylesheet" href="/static/css/chat.css">
    <link rel="stylesheet" href="/static/css/main.css">
</head>

<body>


    <?php

    echo '<div class="main-page-container">';

    echo '<div class="curr-user-info">
    <img onerror="this.onerror=null; this.src=\'/uploads/avatars/default_avatar.jpg\';" class="img-small" width="100px" height="100px" src="/' . htmlspecialchars($user["avatar"], ENT_QUOTES, 'UTF-8') . '" />
<a href="profile.php" class="header-text" >' . htmlspecialchars($user["login"], ENT_QUOTES, 'UTF-8') . '</a>
        </div>';


    // Проверка, есть ли результаты
    if ($result->num_rows > 0) {
        // Начинаем таблицу
        echo "<table border='1' class='main-table'>
            <tr>
                <th>ID</th>
                <th>Логин</th>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Электронная почта</th>
                <th>Дата рождения</th>
                <th>Город</th>
                <th>Аватар</th>
                <th>Написать</th>
            </tr>";

        // Выводим данные пользователей
        while ($row = $result->fetch_assoc()) {
            // Получаем название города
            $citySql = "SELECT name, lat, lng FROM city WHERE id = ?";
            $cityStmt = $conn->prepare($citySql);
            $cityStmt->bind_param("i", $row['city_id']);
            $cityStmt->execute();
            $cityResult = $cityStmt->get_result();

            if ($cityResult->num_rows > 0) {
                $cityData = $cityResult->fetch_assoc();
                $cityName = $cityData['name'];
                $cityLat = $cityData['lat'];
                $cityLng = $cityData['lng'];
            } else {
                $cityName = 'Не указан';
                $cityLat = $cityLng = '';
            }

            $chatRef = "chat.php?user_id=" . $row['id'];
            $userRef = "profile.php?user_id=" . $row['id'];

            // Выводим строку с данными
            echo "<tr>
            <td>" . $row['id'] . "</td>
            <td><a href='" . $userRef . "'>" . $row['login'] . "</a></td>
            <td>" . $row['surname'] . "</td>
            <td>" . $row['name'] . "</td>
            <td>" . $row['patronymic'] . "</td>
            <td>" . $row['email'] . "</td>
            <td>" . $row['birthday'] . "</td>";
            

            if ($cityLat && $cityLng) {
                echo "<td><a href='https://yandex.ru/maps/?ll=" . urlencode($cityLng) . "%2C" . urlencode($cityLat) . "' target='_blank'>" . htmlspecialchars($cityName) . "</a></td>";
            } else {
                echo "<td>" . htmlspecialchars($cityName) . "</td>";
            }

            if ($row['avatar']) {
                echo "<td><img src='" . $row['avatar'] . "' alt='Аватар' width='100'  onerror=\"this.onerror=null; this.src='/uploads/avatars/default_avatar.jpg';\"></td>
            <td><a href='" . $chatRef . "'>Написать</a></td>
         ";
            } else {
                echo "<td><img width='100' src='/uploads/avatars/default_avatar.jpg'></td>
            <td><a href='" . $chatRef . "'>Написать</a></td>
         ";
            }
            
            echo "</tr>";

        }

        // Закрытие таблицы
        echo "</table>";
    } else {
        echo "Нет пользователей для отображения.";
    }

    echo '</div>';

    // Закрытие соединения с базой данных
    $stmt->close();
    $conn->close();
    ?>