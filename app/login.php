<?php
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: /login");
    exit();
} 

$error_message = ''; // Переменная для хранения сообщения об ошибке

if (isset($_POST['login_submit'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $hashed_password = md5($password);
        $stmt->bind_param("ss", $email, $hashed_password);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user) {
            $_SESSION['id_user'] = $user['id'];
            header("Location: /main");
            exit();
        } else {
            $error_message = 'Неверная почта или пароль';
        }
    } else {
        $error_message = 'Пожалуйста, заполните все поля.';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат</title>
    <link rel="stylesheet" href="/static/css/styles.css">
    <link rel="stylesheet" href="/static/css/auth.css">
</head>
<body>
    <div class="login-container">
        <form method="POST" class="login-form">
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <input type="email" name="email" required class="input-field" placeholder="Почта"><br>
            <input type="password" name="password" required class="input-field" placeholder="Пароль"><br>
            <input type="submit" name="login_submit" value="Войти" class="submit-button"><br>
            <p class="message">Не зарегистрированы? <a href="/register" class="register-link">Создайте аккаунт</a></p>
        </form>
    </div>
</body>
</html>
