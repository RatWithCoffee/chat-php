<?php
include("utils/start_settings.php");
include("header.php");

if (!isset($_SESSION['id_user'])) {
    echo "Пользователь не авторизован.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат</title>
    <link rel="stylesheet" href="/static/css/styles.css">
</head>
<style>
     :root {
        --card-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        --hover-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    body {
        font-family: Arial, sans-serif;
        background-color: var(--back-color);
        color: var(--text-color);
        margin: 0;
        padding: 0;
    }

    .container {
        width: 90%;
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
        background-color: var(--main-color);
        border-radius: var(--rounder-border-radius);
        
        /* box-shadow: var(--card-shadow); */
    }

    h1 {
        font-size: 2.5rem;
        color: var(--text-color);
        text-align: center;
        margin-bottom: 30px;
    }

    .card {
        background-color: var(--back-color);
        border: 1px solid var(--border-color);
        border-radius: var(--rounder-border-radius);
        padding: 20px;
        margin-bottom: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: var(--card-shadow);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: var(--hover-shadow);
    }

    .author-info {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        margin-right: 15px;
        object-fit: cover;
        border: 2px solid var(--border-color);
    }

    .author-details {
        font-size: 0.9rem;
    }

    .author-details a {
        text-decoration: none;
        color: var(--text-color);
        font-weight: bold;
    }

    .author-details a:hover {
        text-decoration: underline;
    }

    h2 {
        font-size: 1.8rem;
        margin: 10px 0 15px;
        color: var(--text-color);
    }

    p {
        line-height: 1.6;
        font-size: 1rem;
        color: var(--text-color);
    }

    p strong {
        color: var(--text-color);
    }
</style>

<body>

    <div class="container">
        <h1>Новости</h1>

        <?php
        // Запрос для получения всех новостей с информацией об авторе
        $query = "SELECT n.id, n.title, n.text, n.creation_time, u.login AS author_name, u.id AS author, u.avatar 
              FROM news n
              LEFT JOIN users u ON n.author = u.id
              ORDER BY n.creation_time DESC";

        $result = $conn->query($query);

        // Проверяем, есть ли результаты
        if ($result && $result->num_rows > 0) {
            // Выводим каждую новость
            while ($row = $result->fetch_assoc()) {
                echo "<div class='card'>";
                echo "<div class='author-info'>";
                if (!empty($row['avatar'])) {
                    echo "<img onerror=\"this.onerror=null; this.src='/uploads/avatars/default_avatar.jpg';\" class='avatar'  src='" . htmlspecialchars($row['avatar']) . "' alt='Аватар автора'>";
                } 
                echo "<div>";
                echo "<p><a style='font-size: 3em;' href='/profile.php?user_id=" . htmlspecialchars($row['author']) . "'>" . htmlspecialchars($row['author_name']) . "</a></p>";
                echo "<p><strong></strong> " . htmlspecialchars($row['creation_time']) . "</p>";
                echo "</div>";
                echo "</div>";
                echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
                echo "<p>" . nl2br(htmlspecialchars($row['text'])) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>Новостей пока нет.</p>";
        }

        // Закрываем соединение
        $conn->close();
        ?>
    </div>

</body>

</html>