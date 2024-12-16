<?php
$servername = 'db';
$username = 'rat';
$password = 'rat';
$dbname = 'db';
$port = '3306';

//Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die('Connection faild: ' . $conn->connect_error);
}
include('login.php');

echo '<head>';
echo '<link rel="stylesheet" href="style.css">';
echo '<title>Main page</title>';
echo '</head>';
echo '<body>';
echo '<center>';
if (isset($_POST['MySubmit'])) {
    $err = '';
    if ($_POST['F'] == '') {
        $err = $err . 'Не введена фамилия<br>';
        $Fcl = 'Fcl';
    } else {
        $F = $_POST['F'];
        $Fcl = '';
    }
    if ($_POST['I'] == '') {
        $err = $err . 'Не введено имя<br>';
        $Icl = 'Icl';
    } else {
        $I = $_POST['I'];
        $Icl = '';
    }
    if ($_POST['O'] == '') {
        $err = $err . 'Не введено отчество<br>';
        $Ocl = 'Ocl';
    } else {
        $O = $_POST['O'];
        $Ocl = '';
    }
    if ($_POST['Email'] == '') {
        $err = $err . 'Не введена электронная почта<br>';
        $Emailcl = 'Emailcl';
    } else {
        $Email = $_POST['Email'];
        $Emailcl = '';
    }
    if ($_POST['Birthday'] == '') {
        $err = $err . 'Не введена дата рождения<br>';
        $Birthdaycl = 'Birthdaycl';
    } else {
        $Birthday = $_POST['Birthday'];
        $Birthdaycl = '';
    }
    if ($_POST['Login'] == '') {
        $err = $err . 'Не введен логин<br>';
        $Logincl = 'Logincl';
    } else {
        $Login = $_POST['Login'];
        $Logincl = '';
    }
    if ($_POST['Password'] == '') {
        $err = $err . 'Не введен пароль<br>';
        $Passwordcl = 'Passwordcl';
    } else {
        $Password = $_POST['Password'];
        $Passwordcl = '';
    }
    if ($_POST['Password2'] == '') {
        $err = $err . 'Не введен пароль<br>';
        $Password2cl = 'Password2cl';
    } else {
        $Password = $_POST['Password2'];
        $Password2cl = '';
    }
    if ($_POST['City'] == '') {
        $err = $err . 'Не введен город<br>';
        $Citycl = 'Citycl';
    } else {
        $City = $_POST['City'];
        $Citycl = '';
    }
    if ($_POST['Password'] != $_POST['Password2']) {
        $err = $err . 'Пароль введен не правильно<br>';
        $Passwordcl = 'Passwordcl';
        $Password2cl = 'Password2cl';
    } else {
        $Passwordcl = '';
        $Password2cl = '';
    }
    if ($err == '') {
        $str = 'INSERT INTO user (F, I, O, email, birth, login, password, registration, id_city) VALUES ("' . $_POST['F'] . '","' . $_POST['I'] . '","' . $_POST['O'] . '","' . $_POST['Email'] . '","' . $_POST['Birthday'] . '","' . $_POST['Login'] . '","' . md5($_POST['Password']) . '",NOW(),"' . $_POST['City'] . '")';
        if ($_FILES['avatar']['tmp_name'] != '') {
            $tn = $_FILES['avatar']['tmp_name'];
            $filetype = $_FILES['avatar']['type'];
            if ($filetype == 'image/jpeg') {
                move_uploaded_file($tn, 'C:/AppServ/www/Task1/Images/' . $_POST['Login'] . '.jpg');
            }
            if ($filetype == 'image/png') {
                move_uploaded_file($tn, 'C:/AppServ/www/Task1/Images/' . $_POST['Login'] . '.png');
            }
            if ($filetype == 'image/svg+xml') {
                move_uploaded_file($tn, 'C:/AppServ/www/Task1/Images/' . $_POST['Login'] . '.svg');
            }

        }
        //echo '<pre>';
        //echo print_r($_FILES['avatar']);
        //echo '<pre/>';
        $conn->query($str);
        $Fcl = '';
        $Icl = '';
        $Ocl = '';
        $Emailcl = '';
        $Birthdaycl = '';
        $Logincl = '';
        $Passwordcl = '';
        $Password2cl = '';
        $RegDatecl = '';
        $Citycl = '';
    } else {
        echo $err;
    }

}
//SQL запрос для выборки данных из таблицы user
$sql = 'SELECT user.id, F, I, O, email, birth, login, password, registration, Name, user.status, role, lat, lng FROM user LEFT JOIN city ON user.id_city=city.id';
$result = $conn->query($sql);
if ($loggedin == 1) {
    //Проверка наличия результата и вывод данных
    if ($result->num_rows > 0) {
        //Вывод данных каждой строки

        $count = 0;
        echo '<table border="1", cellspacing="1", cellpadding="1">';
        echo '<tr><th>ID</th><th>Avatar</th><th>F</th><th>I</th><th>O</th><th>Email</th><th>Birthday</th><th>Login</th><th>Password</th><th>Reg. Date</th><th>City</th><th>Status</th><th>Role</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            $ava = './Images/icon.png';
            if (file_exists('C:/AppServ/www/Task1/Images/' . $row['login'] . '.jpg')) {
                $ava = './Images/' . $row['login'] . '.jpg';
            }
            if (file_exists('C:/AppServ/www/Task1/Images/' . $row['login'] . '.png')) {
                $ava = './Images/' . $row['login'] . '.png';
            }
            if (file_exists('C:/AppServ/www/Task1/Images/' . $row['login'] . '.svg')) {
                $ava = './Images/' . $row['login'] . '.svg';
            }

            echo '<td>' . $row['id'] . '</td><td><a target="_blank" href="message.php?id_to=' . $row['id'] . '"><img src="' . $ava . '" width="50px"></a></td><td>' . $row['F'] . '</td><td>' . $row['I'] . '</td><td>' . $row['O'] . '</td><td>' . $row['email'] . '</td><td>' . $row['birth'] . '</td><td>' . $row['login'] . '</td><td>' . $row['password'] . '</td><td>' . $row['registration'] . '</td><td><a href="https://yandex.ru/maps/?ll=' . $row['lng'] . '%2C' . $row['lat'] . '">' . $row['Name'] . '</a></td><td>' . $row['status'] . '</td><td>' . $row['role'] . '</td>';
            echo '</tr>';
            $count = $count + 1;
        }
        echo '</table>';

    } else {
        echo '0 results';
    }
} else {
    $citsel = 'SELECT * FROM city';
    $res = $conn->query($citsel);
    echo '<form enctype="multipart/form-data" action="" method = "POST" href=".\Task1\01.php" name="MyForm">';
    echo 'Фамилия:<input type="text" name="F" id="F" class = "' . $Fcl . '" value="' . $F . '"/><br>';
    echo 'Имя:<input type="text" name="I" id="I" class = "' . $Icl . '" value="' . $I . '"/><br>';
    echo 'Отчество:<input type="text" name="O" id="O" class = "' . $Ocl . '" value="' . $O . '"/><br>';
    echo 'Почта:<input type="text" name="Email" id="email" class = "' . $Emailcl . '" value="' . $Email . '"/><br>';
    echo 'День рождения:<input type="text" name="Birthday" id="birth" class = "' . $Birthdaycl . '" value="' . $Birthday . '"/><br>';
    echo 'Логин:<input type="text" name="Login" id="login" class = "' . $Logincl . '" value="' . $Login . '"/><br>';
    echo 'Пароль:<input type="password" name="Password" id="password" class = "' . $Passwordcl . '" value="' . $Password . '"/><br>';
    echo 'Повторите пароль:<input type="password" name="Password2" id="password2" class = "' . $Password2cl . '" value="' . $Password2 . '"/><br>';
    //echo '<input type="text" name="RegDate" id="registration" class = "'.$RegDatecl.'" value="'.$RegDate.'"/><br>';
    echo '<select name="City" id="id_city" class = "' . $Citycl . '" value="' . $City . '"/>';
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            echo '<option value="' . $row['id'] . '">' . $row['Name'] . '</option>';
        }
    }
    echo '</select><br>';
    echo '<input type="file" id="Avatar" name="avatar"/><br>';
    echo '<input type="submit" name="MySubmit"/><br>';
    echo '</form>';
}
echo '</center>';
$conn->close();
?>