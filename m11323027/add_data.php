<?php
session_start(); //啟動SESSION

//檢查是否有正常登入 BY檢查裡面有沒有Login_user
if(!isset($_SESSION["login_user"]))
{
    //沒有正常登入，跳回index.php
	echo "<script type='text/javascript'>alert('注意！請登入再使用')</script>";
    header("location:index.php");
}

//新增留言
include_once 'dbconfig.php'; //執行資料庫連線
if(isset($_POST['btn-save']))
{
    $content = $_POST['content']; //取得使用者留言
    $username = $_SESSION["login_user"]; //取得登入用戶名
    
    // 獲取使用者的nickname
    $sql_user = "SELECT nickname FROM users WHERE username = '$username'";
    $result_user = mysqli_query($link, $sql_user);
    $user_row = mysqli_fetch_assoc($result_user);
    $nickname = $user_row['nickname'];

    $content_xx = mysqli_real_escape_string($link, $content); //避免SQL Injection
    $nickname_xx = mysqli_real_escape_string($link, $nickname);

    //將新增的content寫入comments資料表
    $sql_query = "INSERT INTO comments(content, nickname) VALUES('$content_xx', '$nickname_xx')";

    if(mysqli_query($link,$sql_query)){
        ?>
        <script type="text/javascript">
        alert('已新增留言!');
        window.location.href='main.php';
        </script>
        <?php
    }
    else{
        ?>
        <script type="text/javascript">
        alert('發生錯誤!');
        </script>
        <?php
    }
}
?>

<style>
.hd{
	background-color: #71A3AF;
}
.form2{
	width: 800px;
	height: 30px;
}
</style>

<!DOCTYPE html>
<html>
<head>
<meta charset=utf-8" />
<title>智慧型資料庫系統實驗室作業 - 新增留言</title>
<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<center>

<div id="header">
	<div id="comment" class="hd">
		<label>新增留言</label>
    </div>
</div>

<div id="body">
	<div id="comment">
    <form method="post" class="form2">
		<table align="center">
			<tr>
				<td align="center"><a href="main.php">回到主頁<br>back to main page</a></td>
			</tr>
			<tr>
				<td><input type="text" name="content" placeholder="留言" required /></td>
			</tr>
			
			<tr>
				<td><button type="submit" name="btn-save" colspan="2"><strong>儲存<br>SAVE</strong></button></td>
			</tr>
		</table>
    </form>
    </div>
</div>

</center>
</body>
</html>