<?php
include("utils/start_settings.php");

// Функция для получения значений из POST-запроса
function getPostValue($key, &$error, $fieldName)
{
    if (empty($_POST[$key])) {
        $error .= 'Не введено ' . $fieldName . '<br>';
        return null;
    }
    return $_POST[$key];
}

$err = '';
$F = $I = $O = $Email = $Birthday = $Login = $Password = $City = $PasswordRepeat = $avatarPath = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['MySubmit'])) {
    // Получение данных из формы
    $F = getPostValue('F', $err, 'фамилия');
    $I = getPostValue('I', $err, 'имя');
    $O = getPostValue('O', $err, 'отчество');
    $Email = getPostValue('Email', $err, 'электронная почта');
    $Birthday = getPostValue('Birthday', $err, 'дата рождения');
    $Login = getPostValue('Login', $err, 'логин');
    $Password = getPostValue('Password', $err, 'пароль');
    $PasswordRepeat = getPostValue('PasswordRepeat', $err, 'поторите пароль');
    $City = getPostValue('City', $err, 'город');
    $data = date("Y-m-d H:i:s"); // Текущая дата и время

    // Проверка на совпадение паролей
    if ($PasswordRepeat != $Password) {
        $err .= 'Пароли не совпадают<br>';
    }

    // Обработка загрузки аватара
    if (!empty($_FILES['avatar']['name'])) {
        $targetDir = "uploads/avatars/";
        $fileName = basename($_FILES['avatar']['name']);
        $targetFilePath = $targetDir . time() . "_" . $fileName; 
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowedTypes = ['jpg', 'jpeg', 'png'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFilePath)) {
                $avatarPath = $targetFilePath; 
            } else {
                $err .= 'Ошибка при загрузке файла<br>';
            }
        } else {
            $err .= 'Недопустимый формат файла. Разрешены: jpg, jpeg, png<br>';
        }
    } 
    // Если нет ошибок, сохраняем данные в базу
    if (empty($err)) {
        $hashedPass = md5($Password); // Хеширование пароля
    
        // Запрос на вставку данных в таблицу users
        $stmt = $conn->prepare('INSERT INTO users (surname, name, patronymic, email, birthday, login, password, registration, avatar, city_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssssi', $F, $I, $O, $Email, $Birthday, $Login, $hashedPass, $data, $avatarPath, $City);
        
        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            session_start();
            $_SESSION['id_user'] = $user_id;
            header("Location: /main.php");
            exit();
        } else {
            $err .= 'Ошибка при регистрации: ' . $stmt->error . '<br>';
        }
    
        $stmt->close();
    }
}

// Получение списка городов для выпадающего списка
$city_sql = "SELECT id, name FROM city";
$city_result = $conn->query($city_sql);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат</title>
    <link rel="stylesheet" href="/static/css/styles.css">
    <link rel="stylesheet" href="/static/css/register.css">
</head>
<body>
    <form method="POST" name="MyForm" enctype="multipart/form-data">
        <div id="error" style="color: red;"><?php echo $err; ?></div>
        <input type="text" name="F" id="F" value="<?php echo $F; ?>" placeholder="Фамилия"/><br>
        <input type="text" name="I" id="I" value="<?php echo $I; ?>" placeholder="Имя"/><br>
        <input type="text" name="O" id="O" value="<?php echo $O; ?>" placeholder="Отчество"/><br>
        <input type="text" name="Email" id="Email" value="<?php echo $Email; ?>" placeholder="Электронная почта"/><br>
        <input type="date" name="Birthday" id="Birthday" value="<?php echo $Birthday; ?>" placeholder="Дата рождения (ГГГГ-ММ-ДД)"/><br>
        <input type="text" name="Login" id="Login" value="<?php echo $Login; ?>" placeholder="Логин"/><br>
        <input type="password" name="Password" id="Password" value="<?php echo $Password; ?>" placeholder="Пароль"/><br>
        <input type="password" name="PasswordRepeat" id="PasswordRepeat" value="<?php echo $PasswordRepeat; ?>" placeholder="Повторите пароль"/><br>
        <input type="file" id="avatar" name="avatar"/><br>
        <select name="City" id="City">
            <option value="">Выберите город</option>
            <?php if ($city_result->num_rows > 0) {
                while ($city = $city_result->fetch_assoc()) {
                    echo '<option value="' . $city['id'] . '" ' . ($City == $city['id'] ? 'selected' : '') . '>' . $city['name'] . '</option>';
                }
            } ?>
        </select><br>
        <input type="submit" name="MySubmit" value="Зарегистрироваться"/><br>
        <p class="message">Уже зарегистрированы? <a href="/login">Войти</a></p>
    </form>

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
$conn->close();
?>
