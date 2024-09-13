<?php
  session_start();
  if(isset($_SESSION["login_user"]))  //若已經正常登入，$_SESSION["login_user"]有資料
  {
	  header("location:main.php");
  }
?>

<style>
.hd{
	background-color: #71A3AF;
}
.button {
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
}
.button1 {background-color: #71A3AF;}
</style>

<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8" />
	<title>智慧型資料庫系統實驗室作業</title>
	<link rel="stylesheet" href="style.css" type="text/css" />

</head>
<body>
<center>

<div id="header">
	<div id="content" class="hd">
		<label>智慧型資料庫系統實驗室作業 - M11323027</label>
    </div>
</div>

<div id="body">
	<div id="content">
		<h1>請登入後再使用</h1>
	</div>
	<div>
		<a href="login.php"><button class="button button1">登入 Login</button></a>
	</div>
</div>

</center>
</body>
</html>