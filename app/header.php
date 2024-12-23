<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Инициализируем сессию только если она еще не начата
}
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: /login.php");
    exit(); // Обязательно завершить выполнение скрипта после перенаправления
}



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
    exit();
}


?>


<div id="user-header" class="header">
    
    <a href="/main.php" class="header-text" style="cursor: pointer;">На главную</a>
    <a href="/news.php" class="header-text" style="cursor: pointer;">Лента новостей</a>
    <a href="/create-news.php" class="header-text" style="cursor: pointer;">Создать новость</a>
    <form action="" method="POST" style="margin: 0; display: flex; justify-content: center;">
        <button type="submit" name="logout" class="header-text" style="cursor: pointer; background: none; border: none; color: inherit;">Выйти</button>
    </form>


    <script>
        const toUserHeader = document.getElementById("user-login");
        toUserHeader.onclick = () => {
            window.location.href = `/profile.php`;
        };
    </script>
</div>

</body>

</html>