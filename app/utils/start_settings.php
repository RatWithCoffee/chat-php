<?php
$servername = 'db';
$username = 'rat';
$password = 'rat';
$dbname = 'db';
$port = '3306';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

