<?php  
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_ALL); 
ini_set("display_errors", 1);

$db_username = 'andrex_1';
$db_password = 'Pro-create69';
$db_name = 'andrex_db1';
$db_host = 'andrew02.it';
$db = new mysqli($db_host, $db_username, $db_password, $db_name);
if (mysqli_connect_errno()) {
    die();
}
$db->set_charset("utf8");
