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