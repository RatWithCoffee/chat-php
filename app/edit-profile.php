<?php

// Получаем ID пользователя из URL (например, profile/2)
$current_url = $_SERVER['REQUEST_URI'];
preg_match('/profile\/(\d+)/', $current_url, $matches);

// Проверяем, что ID найден
if (isset($matches[1])) {
    $user_id = $matches[1]; // Извлекаем ID пользователя из URL
} else {
    // если id нет - берем id текущего пользователя из сессии
    $user_id = intval($_SESSION['id_user']);
}


// Получаем информацию о пользователе
$sql_user = "SELECT u.id, u.surname, u.name, u.patronymic, u.email, u.birthday, u.login, u.registration, u.avatar, c.name AS city_name, c.id AS city_id
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


// Обработка формы обновления данных
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $surname = htmlspecialchars($_POST['surname']);
    $name = htmlspecialchars($_POST['name']);
    $patronymic = htmlspecialchars($_POST['patronymic']);
    $email = htmlspecialchars($_POST['email']);
    $birthday = $_POST['birthday'];
    $city_id = intval($_POST['city_id']);

    $avatar = $user['avatar']; // Сохраняем текущий аватар, если новый не загружается
    if (!empty($_FILES['avatar']['name'])) {
        $upload_dir = __DIR__ . '/uploads/avatars/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

        // Проверяем тип файла
        if (in_array($_FILES['avatar']['type'], $allowed_types)) {
            $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $extension; // Уникальное имя файла
            $upload_path = $upload_dir . $new_filename;
            

            // Перемещаем файл в папку загрузки
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                $avatar = 'uploads/avatars/' . $new_filename; // Обновляем имя файла аватара
            } 
        } 
    }

    $sql_update = "UPDATE users SET surname = ?, name = ?, patronymic = ?, email = ?, birthday = ?, city_id = ?, avatar = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssssisi", $surname, $name, $patronymic, $email, $birthday, $city_id, $avatar, $user_id);

    if ($stmt_update->execute()) {
        header("Location: /profile"); // Перенаправление на страницу профиля
        exit();
    } else {
        echo("here");
        $err = "Пользователь с таким email уже есть";
    }
}


include('header.php');


// Получаем список городов
$sql_cities = "SELECT id, name FROM city";
$result_cities = $conn->query($sql_cities);

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование профиля</title>
    <link rel="stylesheet" href="/static/css/edit-profile.css">
</head>

<body>
    <div class="container">
        <h1>Редактирование профиля</h1>
        <form method="POST" class="edit-form"  enctype="multipart/form-data">
            <label for="curr-avatar">Текущая фотография:</label>
            <div class="current-avatar">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" width="350" alt="Аватар" class="avatar">
                <?php else: ?>
                    <p>Фотография не загружена</p>
                <?php endif; ?>
            </div>

            <label for="avatar">Загрузить новую фотографию:</label>
            <input type="file" id="avatar" name="avatar" accept="image/*">

            <label for="surname">Фамилия:</label>
            <input type="text" id="surname" name="surname" value="<?php echo $user['surname']; ?>" required>

            <label for="name">Имя:</label>
            <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>

            <label for="patronymic">Отчество:</label>
            <input type="text" id="patronymic" name="patronymic" value="<?php echo $user['patronymic']; ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>

            <label for="birthday">Дата рождения:</label>
            <input type="date" id="birthday" name="birthday" value="<?php echo $user['birthday']; ?>">

            <label for="city">Город:</label>
            <select id="city" name="city_id">
                <?php while ($city = $result_cities->fetch_assoc()): ?>
                    <option value="<?php echo $city['id']; ?>" <?php echo ($city['id'] == $user['city_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($city['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Сохранить изменения</button>
        </form>


        <a href="/profile">Вернуться к профилю</a>
    </div>
    <script>
        // Если ошибки есть, отобразить их в элементе с id="error"
        const errorMessage = "<?php echo addslashes($err); ?>";
        if (errorMessage) {
            document.getElementById('error').innerHTML = errorMessage;
        }
    </script>
</body>

</html>

<?php
$stmt_user->close();
$conn->close();
?>