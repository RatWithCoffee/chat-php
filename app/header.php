<?php
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: /login");
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
    <link rel="stylesheet" href="/static/css/chat.css">
    <link rel="stylesheet" href="/static/css/main.css">
</head>
<body>

<div id="user-header" class="header">
    <img class="img-small" width="100px" height="100px" src="/<?php echo htmlspecialchars($user['avatar']); ?>" />
    <div id="user-login" class="header-text" style="cursor: pointer;"><?php echo htmlspecialchars($user['login']); ?></div>
    <a href="/main" class="header-text" style="cursor: pointer;">На главную</a>
    <form method='POST'>
        <button type='submit' name='logout'>Выйти</button>
    </form>

    <script>
        const toUserHeader = document.getElementById("user-login");
        toUserHeader.onclick = () => {
            const to_user = parseInt(window.location.href.substring(window.location.href.lastIndexOf('/') + 1));
            if (!isNaN(to_user)) {
                window.location.href = `/profile/${to_user}`;
            } else {
                window.location.href = `/profile/`;
            }
        };
    </script>
</div>

</body>
</html>
