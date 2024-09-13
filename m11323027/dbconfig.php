<?php
$host = "localhost";
$user = "root"; 
$password = ""; 
$database = "m11323027";

//連結資料庫
$link = mysqli_connect($host, $user, $password, $database);
//編碼設定
mysqli_query($link, "SET NAMES utf8");
//設定成臺灣時區
$link->query('SET time_zone = "+8:00"');
?>