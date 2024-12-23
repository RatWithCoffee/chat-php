<?php
// Проверяем, что id найдено
$to_user_id = $_GET['user_id'] ?? null;

// Проверяем, что ID передан и является числом
if (!($to_user_id && is_numeric($to_user_id))) {
    echo "ID пользователя не верен";
    exit();
}

include("utils/start_settings.php");
// Получаем информацию о пользователе
$sql_user = "SELECT u.id, u.surname, u.name, u.patronymic, u.email, u.birthday, u.login, u.registration, u.avatar, c.name AS city_name
             FROM users u
             LEFT JOIN city c ON u.city_id = c.id
             WHERE u.id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $to_user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();



if ($result_user->num_rows > 0) {
    // Получаем данные пользователя
    $user = $result_user->fetch_assoc();
} else {
    exit();
}

include('header.php');
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат</title>
    <link rel="stylesheet" href="/static/css/styles.css">
    <link rel="stylesheet" href="/static/css/main.css">
    <link rel="stylesheet" href="/static/css/chat.css">
</head>

<?php

$user_id = $_SESSION['id_user'];

// Обработка отправки нового сообщения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_text'])) {
    $message_text = $_POST['message_text'];
    $img_name = ''; // Если есть изображение, обрабатывайте его тут

    if (isset($_FILES['message_image']) && $_FILES['message_image']['error'] == 0) {
        $img_tmp = $_FILES['message_image']['tmp_name'];
        $img_name = uniqid('msg_', true) . '.' . pathinfo($_FILES['message_image']['name'], PATHINFO_EXTENSION);
        $img_path = 'uploads/chat/' . $img_name;

        // Проверка типа файла (только изображения)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['message_image']['type'], $allowed_types)) {
            // Перемещение изображения в папку
            if (!move_uploaded_file($img_tmp, $img_path)) {
                echo "Error uploading image.";
                exit;
            }
        } else {
            echo "Invalid image format.";
            exit;
        }
    }

    // Подготовка и выполнение запроса на добавление сообщения в базу данных
    $stmt = $conn->prepare("INSERT INTO message (text, from_user, to_user, sending_time, `read`, img_name) 
                            VALUES (?, ?, ?, NOW(), 0, ?)");
    $stmt->bind_param("siis", $message_text, $user_id, $to_user_id, $img_name); // Привязка параметров

    // Определите ID получателя (можно добавить логику для получения ID другого пользователя)
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Получение сообщений из базы данных
$sql = "SELECT * FROM message WHERE (from_user = ? AND to_user = ?) OR (from_user = ? AND to_user = ?) ORDER BY sending_time ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $user_id, $to_user_id, $to_user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$stmt->close();
?>


<div class="chat-container">
    <div id="messages-container" class="messages-container">
        <?php
        while ($row = $result->fetch_assoc()) {
            // Если сообщение от текущего пользователя
            if ($user_id == $row['from_user']) {
                echo "<div class='text-chat text-chat_reply animate-fadeinup' data-id='" . $row['id'] . "'>";
                echo "<div class='text-chat--container'>";
                if (!$row["read"]) {
                    echo "<div class='text-chat--unread-dot'></div>";
                }
                echo "<div class='text-chat--text'>";
                echo "<p>" . htmlspecialchars($row['text']) . "</p>";
                echo "<p>" . htmlspecialchars($row['sending_time']) . "</p>";

                // Если есть изображение, показываем его
                if (!empty($row['img_name'])) {
                    echo "<div class='text-chat--image'>";
                    echo "<img onerror='this.onerror=null; this.src='/uploads/avatars/default_avatar.jpg';' width='100px' height='100px' src='/uploads/chat/" . htmlspecialchars($row['img_name']) . "' />";
                    echo "</div>";
                }

                echo "</div>";
                echo "</div>";
                echo "</div>";
            } else {
                // Если сообщение от другого пользователя
                echo "<div class='text-chat animate-fadeinup data-id='" . $row['id'] . "''>";
                echo "<div class='text-chat--container'>";
                echo "<div class='text-chat--text'>";
                echo "<p>" . htmlspecialchars($row['text']) . "</p>";
                echo "<p>" . htmlspecialchars($row['sending_time']) . "</p>";

                // Если есть изображение, показываем его
                if (!empty($row['img_name'])) {
                    echo "<div class='text-chat--image'>";
                    echo "<img 'onerror='this.onerror=null; this.src='/uploads/avatars/default_avatar.jpg';' width='100px' height='100px' src='/uploads/chat/" . htmlspecialchars($row['img_name']) . "'/>";
                    echo "</div>";
                }

                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        }

        ?>
    </div>
    <form id="message-form" class="new-msg-form" enctype="multipart/form-data">
        <input name="message_text" class="textarea-msg" required id="new-msg" />

        <!-- Кнопка для выбора файла -->
        <label for="file-input" class="file-input-label" style="cursor: pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#FFFF"
                version="1.1" id="Capa_1" width="30px" height="30px" viewBox="0 0 605.942 605.942"
                xml:space="preserve">
                <g>
                    <g>
                        <path
                            d="M435.099,0c-45.632,0-88.544,17.777-120.822,50.055L50.036,314.276c-66.603,66.622-66.603,175.018,0,241.64    c32.269,32.259,75.171,50.026,120.808,50.026s88.544-17.768,120.812-50.026l19.757-19.751l-12.321,0.588    c-0.062,0.005-0.728,0.033-1.889,0.033c-5.709,0-25.776-0.746-44.309-10.312l-2.815-1.454l-2.43,2.027    c-21.425,17.915-48.706,27.779-76.806,27.779c-32.034,0-62.118-12.432-84.714-35.009c-22.592-22.591-35.037-52.68-35.037-84.724    s12.445-62.127,35.037-84.724L350.37,86.149c22.592-22.591,52.685-35.037,84.729-35.037c32.04,0,62.128,12.446,84.724,35.037    c46.666,46.703,46.666,122.696,0.005,169.399L354.798,420.19c-8.291,8.306-19.771,12.881-32.331,12.881    c-13.641,0-26.847-5.394-36.242-14.808c-9.018-9.008-14.181-21.563-14.176-34.463c0-12.604,4.776-24.304,13.431-32.938    L432.541,204.36l-36.099-36.094L249.366,314.75c-18.479,18.479-28.539,43.284-28.334,69.84    c0.206,26.444,10.538,51.222,29.09,69.772c18.871,18.867,45.226,29.687,72.321,29.687c26.244,0,50.566-9.863,68.486-27.765    l164.986-164.628c66.589-66.631,66.589-175.018,0-241.601C523.643,17.777,480.731,0,435.099,0z" />
                    </g>
                </g>
            </svg>
        </label>
        <input type="file" name="message_image" accept="image/*" id="file-input">

        <!-- Отображение имени файла -->
        <span id="file-name" class="file-name"></span>


        <button class="button-send" type="submit">
            <svg width="30px" height="30px" viewBox="0 0 24 24" fill="#0000" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 2L2 8.66667L11.5833 12.4167M22 2L15.3333 22L11.5833 12.4167M22 2L11.5833 12.4167"
                    stroke="#FFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </form>
    <script src="/static/js/chat.js"></script>
</div>

<?php
$sql = "UPDATE message SET `read` = 1 WHERE to_user = ?";
$stmt_user = $conn->prepare($sql);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$conn->close();
?>