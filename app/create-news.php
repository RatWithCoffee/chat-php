<?php
include("utils/start_settings.php");
include("header.php");

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['id_user'])) {
    echo "Пользователь не авторизован.";
    exit();
}

// Подключаем настройки и базу данных
include("utils/start_settings.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $title = $_POST['title'] ?? '';
    $text = $_POST['text'] ?? '';
    $author = $_SESSION['id_user']; // ID текущего пользователя

    // Проверяем заполненность полей
    if (empty($title) || empty($text)) {
        echo "Все поля должны быть заполнены.";
    } else {
        // SQL-запрос для добавления новости
        $query = "INSERT INTO news (title, text, author, creation_time) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Привязываем параметры
            $stmt->bind_param('ssi', $title, $text, $author);

            // Выполняем запрос
            if ($stmt->execute()) {
                echo "<div class='notification success'>Новость успешно добавлена.</div>";
            } else {
                echo "Ошибка при добавлении новости: " . $stmt->error;
            }

            // Закрываем запрос
            $stmt->close();
        } else {
            echo "Ошибка подготовки запроса: " . $conn->error;
        }
    }
}
?>
<style>
.main-form-container {
    margin-top: 30px;
    display: flex;
    justify-content: center; /* Центрирование по горизонтали */
    align-items: center; /* Центрирование по вертикали */
    background-color: var(--main-color); /* Цвет фона */
    color: var(--text-color);
}

.form-container {
    display: flex;
    justify-content: center; /* Центрирование по горизонтали */
    align-items: center; /* Центрирование по вертикали */
    min-height: 100vh; /* Высота контейнера - 100% от высоты экрана */
    background-color: var(--main-color); /* Фон страницы */
}

.creation-form {
    background-color: var(--back-color); /* Цвет фона формы */
    padding: 20px;
    border-radius: var(--rounder-border-radius);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Тень вокруг формы */
    width: 100%;
    max-width: 600px; /* Максимальная ширина формы */
}

label {
    font-size: 1rem;
    font-weight: bold;
    color: var(--text-color);
}

input[type="text"], textarea {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border-radius: var(--rounder-border-radius);
    border: 1px solid var(--border-color);
    background-color: var(--back-color);
    color: var(--text-color);
}

input[type="text"]:focus, textarea:focus {
    border-color: var(--blue-color);
    outline: none;
}

button[type="submit"] {
    background-color: var(--blue-color); /* Цвет кнопки */
    color: var(--back-color); /* Цвет текста */
    padding: 12px 20px;
    border: none;
    border-radius: var(--rounder-border-radius);
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
}

button[type="submit"]:hover {
    background-color: var(--hover-color); /* Цвет кнопки при наведении */
    transform: translateY(-2px); /* Эффект при наведении */
}

button[type="submit"]:active {
    transform: translateY(0); /* Эффект при нажатии */
}



.notification {
    padding: 20px;
    margin: 20px 20px;
    border-radius: var(--rounder-border-radius);
    font-size: 1.2rem;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: opacity 0.5s ease-out;
}

.notification.success {
    background-color: var(--dark-blue-color); /* Цвет фона для успешного сообщения */
}

.notification.error {
    background-color: var(--red-color); /* Цвет фона для ошибки */
}

.notification p {
    margin: 0;
    padding: 0;
}

.notification .close-btn {
    background: transparent;
    border: none;
    color: #fff;
    font-size: 1.5rem;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.notification .close-btn:hover {
    transform: scale(1.2);
}

/* Анимация для появления уведомления */
.notification {
    opacity: 0;
    animation: fadeIn 1s forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}


</style>
<body>
    <div class="main-form-container">
    <form method="post" action="" class="creation-form">
        <label for="title">Заголовок:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="text">Текст новости:</label><br>
        <textarea id="text" name="text" rows="10" cols="50" required></textarea><br><br>

        <button type="submit">Добавить новость</button>
        <link rel="stylesheet" href="/static/css/styles.css">
    </form>
    </div>
</body>
</html>
