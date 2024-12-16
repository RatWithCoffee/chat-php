<?php
$servername = 'postgres'; 
$username = 'rat';
$password = 'rat';
$dbname = 'db';
$port = '5432'; 
// // Создание подключения
// $conn = pg_connect("host=$servername port=$port dbname=$dbname user=$username password=$password");

// // Проверка подключения
// if (!$conn) {
//     die('Connection failed: ' . pg_last_error());
// }

// // SQL запрос для выборки данных из таблицы user
// $sql = 'SELECT * FROM "user"'; 
// $result = pg_query($conn, $sql);

// // Проверка наличия результатов и вывод данных
// if (pg_num_rows($result) > 0) {
//     // Вывод данных каждой строки
//     while ($row = pg_fetch_assoc($result)) {
//         echo '<div style="background:rgb(' . rand(0,255) . ',' . rand(0,255) . ',' . rand(0,255) . ')"> Фамилия: ' . $row['F'] . ' , Email: ' . $row['email'] . '</div></br>';
//     }
// } else {
//     echo '0 results';
// }

// // Закрытие подключения
// pg_close($conn);
?>
