<?php
$current_url = $_SERVER['REQUEST_URI'];
preg_match('/chat\/(\d+)/', $current_url, $matches);

// Проверяем, что id найдено
if (isset($matches[1])) {
    $to_user_id = $matches[1]; // id текущего пользователя
} else {
    die("Invalid URL or user ID not found.");
}

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
    echo "Пользователь не найден.";
    exit();
}
include('header.php');
?>


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
                echo "<div class='text-chat text-chat_reply animate-fadeinup'>";
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
                    echo "<img width='100px' height='100px' src='/uploads/chat/" . htmlspecialchars($row['img_name']) . "'/>";
                    echo "</div>";
                }

                echo "</div>";
                echo "</div>";
                echo "</div>";
            } else {
                // Если сообщение от другого пользователя
                echo "<div class='text-chat animate-fadeinup'>";
                echo "<div class='text-chat--container'>";
                echo "<div class='text-chat--text'>";
                echo "<p>" . htmlspecialchars($row['text']) . "</p>";
                echo "<p>" . htmlspecialchars($row['sending_time']) . "</p>";

                // Если есть изображение, показываем его
                if (!empty($row['img_name'])) {
                    echo "<div class='text-chat--image'>";
                    echo "<img width='100px' height='100px' src='/uploads/chat/" . htmlspecialchars($row['img_name']) . "'/>";
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
        <input type="file" name="message_image" accept="image/*" id="file-input">
        <label for="file-input">Выбрать файл</label> <!-- Связываем label с input через атрибут for -->

        <!-- Отображение имени файла -->
        <span id="file-name" class="file-name"></span>

        <button class="button-send" type="submit">Отправить</button>
    </form>
    <script>
        // Получаем элементы
        const fileInput = document.getElementById('file-input');
        const fileNameDisplay = document.getElementById('file-name');

        // Обработчик события на изменение (выбор файла)
        fileInput.addEventListener('change', function () {
            const fileName = fileInput.files[0] ? fileInput.files[0].name : ''; // Получаем имя файла
            if (fileName) {
                fileNameDisplay.textContent = `Прикреплен файл: ${fileName}`; // Отображаем имя файла
            } else {
                fileNameDisplay.textContent = ''; // Если файл не выбран, скрываем текст
            }
        });

        const chatContainer = document.getElementById('messages-container')

        document.getElementById('message-form').addEventListener('submit', function (event) {
            event.preventDefault(); // Останавливаем стандартную отправку формы

            // Получаем данные формы
            var formData = new FormData(this);

            // Получаем файл из формы (если выбран)
            var fileInput = document.getElementById('file-input');
            var file = fileInput.files[0];

            if (file) {
                var reader = new FileReader();
                reader.onloadend = function () {
                    // Когда файл будет загружен, получаем его строку Base64
                    var base64file = reader.result;

                    // Добавляем Base64 строку в FormData
                    formData.append('message_image_base64', base64file);

                    // Отправляем данные на сервер через AJAX
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '', true); // Пустой URL, т.к. форма отправляется на тот же URL

                    // Устанавливаем обработчик для успешного ответа
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            // Очистить поле ввода и скрыть имя файла после успешной отправки
                            let now = new Date();

                            let formattedDate = now.getFullYear() + '-' +
                                String(now.getMonth() + 1).padStart(2, '0') + '-' +
                                String(now.getDate()).padStart(2, '0') + ' ' +
                                String(now.getHours()).padStart(2, '0') + ':' +
                                String(now.getMinutes()).padStart(2, '0') + ':' +
                                String(now.getSeconds()).padStart(2, '0');

                            // Вставляем картинку в сообщение
                            const imgDiv = getImgDiv(base64file);

                            const msg = `
                            <div class="text-chat text-chat_reply animate-fadeinup">
                                <div class="text-chat--container">
                                    <div class="text-chat--unread-dot"></div>
                                    <div class="text-chat--text">
                                        <p>${document.getElementById('new-msg').value}</p>
                                        <p>${formattedDate}</p>
                                        ${imgDiv}
                                    </div>
                                </div>
                            </div>
                        `;
                            chatContainer.insertAdjacentHTML('beforeend', msg);

                        } else {
                            alert('Произошла ошибка при отправке сообщения.');
                        }
                        document.getElementById('new-msg').value = '';
                        document.getElementById('file-name').textContent = '';
                    };

                    // Отправляем форму
                    xhr.send(formData);
                };

                // Читаем файл как Data URL (Base64)
                reader.readAsDataURL(file);
            } else {
                // Если файла нет, отправляем форму без изображения
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '', true); // Пустой URL, т.к. форма отправляется на тот же URL

                // Устанавливаем обработчик для успешного ответа
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        // Очистить поле ввода после успешной отправки
                        let now = new Date();

                        let formattedDate = now.getFullYear() + '-' +
                            String(now.getMonth() + 1).padStart(2, '0') + '-' +
                            String(now.getDate()).padStart(2, '0') + ' ' +
                            String(now.getHours()).padStart(2, '0') + ':' +
                            String(now.getMinutes()).padStart(2, '0') + ':' +
                            String(now.getSeconds()).padStart(2, '0');

                        const msg = `
                        <div class="text-chat text-chat_reply animate-fadeinup">
                            <div class="text-chat--container">
                                <div class="text-chat--unread-dot"></div>
                                <div class="text-chat--text">
                                    <p>${document.getElementById('new-msg').value}</p>
                                    <p>${formattedDate}</p>
                                </div>
                            </div>
                        </div>
                    `;
                        chatContainer.insertAdjacentHTML('beforeend', msg);

                    } else {
                        alert('Произошла ошибка при отправке сообщения.');
                    }
                    document.getElementById('new-msg').value = '';
                    document.getElementById('file-name').textContent = '';
                };

                // Отправляем форму
                xhr.send(formData);
            }
        });

        const getImgDiv = (base64file) => {
            if (!base64file) {
                return "";
            }
            return `
            <div class="text-chat--image">
                <img width="100px" height="100px" src="${base64file}"/>
            </div>
        `;
        };
    </script>

</div>

<?php
$sql = "UPDATE message SET `read` = 1 WHERE to_user = ?";
$stmt_user = $conn->prepare($sql);
$stmt_user->bind_param("i", $to_user_id);
$stmt_user->execute();
$conn->close();
?>