<?php
if (!defined('DB_USER')) define('DB_USER','root');
if (!defined('DB_PASSWORD')) define('DB_PASSWORD','');
if (!defined('DB_HOST')) define('DB_HOST','localhost');
if (!defined('DB_NAME')) define('DB_NAME','ql_ban_sofa');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die('Không thể kết nối MySQL: ' . $conn->connect_error);
}

$conn->set_charset("utf8");
$dbc = $conn;
?>
