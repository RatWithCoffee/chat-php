<?php
include("utils/start_settings.php");
include('header.php');

$curr_user_id = $_SESSION['id_user'];


// Получаем информацию о пользователе
$sql_user = "SELECT u.id, u.surname, u.name, u.patronymic, u.email, u.birthday, u.login, u.registration, u.avatar, c.name AS city_name
             FROM users u
             LEFT JOIN city c ON u.city_id = c.id
             WHERE u.id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $curr_user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    // Получаем данные пользователя
    $user = $result_user->fetch_assoc();
} else {
    echo "Пользователь не найден.";
    exit();
}
///////////////

$user_id = $_GET['user_id'] ?? null;

// Проверяем, что ID передан и является числом
if (!($user_id && is_numeric($user_id))) {
    $user_id = intval($_SESSION['id_user']);
}

// Получаем информацию о пользователе
$sql_user = "SELECT u.id, u.surname, u.name, u.patronymic, u.email, u.birthday, u.login, u.registration, u.avatar, c.name AS city_name
             FROM users u
             LEFT JOIN city c ON u.city_id = c.id
             WHERE u.id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    // Получаем данные пользователя
    $user = $result_user->fetch_assoc();
} else {
    echo "Пользователь не найден.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
    <link rel="stylesheet" href="/static/css/styles.css">
    <link rel="stylesheet" href="/static/css/profile.css">
</head>

<body>

    <div class="container">
        <h1>Профиль пользователя</h1>

        <div class="profile-card">
            <div class="avatar">
                <img src="/<?php echo $user['avatar']; ?>" alt="Avatar" width="350"  onerror="this.onerror=null; this.src='/uploads/avatars/default_avatar.jpg';">
            </div>

            <div class="user-info">
                <p><strong>Фамилия:</strong> <?php echo $user['surname']; ?></p>
                <p><strong>Имя:</strong> <?php echo $user['name']; ?></p>
                <p><strong>Отчество:</strong> <?php echo $user['patronymic']; ?></p>
                <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                <p><strong>Дата рождения:</strong> <?php echo $user['birthday']; ?></p>
                <p><strong>Город:</strong> <?php echo $user['city_name']; ?></p>
                <p><strong>Дата регистрации:</strong> <?php echo $user['registration']; ?></p>
                <p><strong>Логин:</strong> <?php echo $user['login']; ?></p>
                <?php if ($curr_user_id == $user_id): ?>
                    <button onclick="window.location.href='/edit-profile.php';">Редактирование профиля</button>
                <?php endif; ?>
            </div>
        </div>
    </div>


</body>

</html>

<?php
// Закрытие соединения с базой данных
$stmt_user->close();
$conn->close();
?>