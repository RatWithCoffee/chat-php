<?php
$servername = 'db';
$username = 'rat';
$password = 'rat';
$dbname = 'db';
$port = '3306';

//Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error){
    die('Connection faild: ' . $conn->connect_error);
}

$sql = 'SELECT MAX(id) FROM message';
$maxid = $conn->query($sql);
$maxid = $maxid->fetch_assoc();
$sql = 'ALTER TABLE message AUTO_INCREMENT =' . $maxid['MAX(id)'];
$maxid['MAX(id)']++;
$conn->query($sql);

// session_start();
$id_from = intval($_SESSION['id_user']);
//echo $id_from;
parse_str($_SERVER['QUERY_STRING'], $id_to);
$id_to = intval($id_to['id_to']);

if (isset($_POST['send_message'])) {
    if (strval($_POST['send_text']) != '') {
        $str = 'INSERT INTO message (`id_from`,`id_to`,
            `text`) VALUES 
            (' . $id_from . ',' . $id_to . ',
            "' . $_POST['send_text'] . '")';
        $conn->query($str);
        $_POST = '';
    }
}

$sql = 'UPDATE message SET `status`=2 WHERE 
    id_from=' . $id_to . ' AND id_to=' . $id_from;
$conn->query($sql);

echo '<head>';
echo '<link rel="stylesheet" href="message.css">';
echo '<title>dialog</title>';
echo '</head>';

echo '<body>';
echo '<center>';
echo '<div class="window wl">';

$sql = 'SELECT * FROM user WHERE id=' . $id_to;
$result = $conn->query($sql);
$result = $result->fetch_assoc();
echo $result['login'];
if (file_exists('C:/AppServ/www/Task1/Images/' . $result['login'] . '.jpg')) {
    echo '<img src=" C:/AppServ/www/Task1/Images/' . $result['login'] . '.jpg">';
} elseif (file_exists('C:/AppServ/www/Task1/Images/'. $result['login'] . '.png')) {
    echo '<img src="C:/AppServ/www/Task1/Images/'. $result['login'] . '.png">';
} elseif (file_exists('C:/AppServ/www/Task1/Images/' . $result['login'] . '.gif')) {
    echo '<img src="C:/AppServ/www/Task1/Images/' . $result['login'] . '.gif">';
} elseif (file_exists('C:/AppServ/www/Task1/Images/' . $result['login'] . '.svg')) {
    echo '<img src="C:/AppServ/www/Task1/Images/' . $result['login'] . '.svg">';
} else {
    echo '<img src="C:/AppServ/www/Task1/Images/icon.png">';
}
echo '<div>' . $result['F'] . ' ' . $result['I'] . '</div>';
echo '</div>';
echo '<div class="window">';
echo '<div class="dialog">';

$sql = 'SELECT * FROM message WHERE
 (`id_from`=' . $id_from . ' AND `id_to`=' . $id_to . ') OR
 (`id_from`=' . $id_to . ' AND `id_to`=' . $id_from . ')
   ORDER BY creation ASC';
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['id_to'] == $id_from) {
            echo '<div class="mes text_to">' . $row['text'] . '<div>'. $row['creation'] . '</div>';
            if ($row['status']==1){
                echo '<div class="not">Непрочитано</div>';
            }
            else{
                echo '<div class="yes">Прочитано</div>';
            } 
            echo '</div>';
            echo '<div class="clear"></div>';
        } else {
            echo '<div class="mes text_from">' . $row['text'] . '<div>'. $row['creation'] . '</div>';
            if ($row['status']==1){
                echo '<div class="not">Непрочитано</div>';
            }
            else{
                echo '<div class="yes">Прочитано</div>';
            }
            echo '</div>';
            echo '<div class="clear"></div>';
        }
    }
} else {
    echo '<center><div class="mes">нет сообщений</div></center>';
}

echo '</div>';
if (file_exists($files_upload . $id_from . '.jpg')) {
    echo '<img class="img_from" src="' . $files_upload . $id_from . '.jpg" alt="none">';
} elseif (file_exists($files_upload . $id_from . '.png')) {
    echo '<img class="img_from" src="' . $files_upload . $id_from . '.png" alt="none">';
} elseif (file_exists($files_upload . $id_from . '.gif')) {
    echo '<img class="img_from" src="' . $files_upload . $id_from . '.gif" alt="none">';
}
echo '<form action="" method="POST" href="#">';
echo '<div><input type="file" id="Photo" name="photo" class="images"/>';
echo '<textarea name="send_text" placeholder="Введите сообщение..."></textarea>';
echo '<input type="submit" name="send_message" value="Отправить"/></div>';
echo '</form>';
echo '</div>';
?>