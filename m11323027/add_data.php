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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content']; // 取得留言
    $username = $_SESSION["login_user"]; // 取得目前登入的使用者名稱

    //取得nickname
    $sql_user = "SELECT nickname FROM users WHERE username = '$username'";
    $result_user = mysqli_query($link, $sql_user);
    $user_row = mysqli_fetch_assoc($result_user);
    $nickname = $user_row['nickname'];

    //新增留言至comments
    $content_xx = mysqli_real_escape_string($link, $content);
    $nickname_xx = mysqli_real_escape_string($link, $nickname);
    $sql_query = "INSERT INTO comments (content, nickname) VALUES ('$content_xx', '$nickname_xx')";

    //上傳檔案處理
    if (mysqli_query($link, $sql_query)) {
        $comment_id = mysqli_insert_id($link); //取得留言的id

        //處理上傳的檔案
        $files = $_FILES['files']; //取得欲上傳的檔案
        $allowedExts = ['pdf', 'doc', 'docx', 'jpg']; //允許的副檔名

        for ($i = 0; $i < count($files['name']); $i++) {
            $fileName = $files['name'][$i]; //取得欲上傳的檔案名稱
            $fileTmpPath = $files['tmp_name'][$i]; //取得檔案的路徑
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION); //取得檔案的副檔名

            //比對檔案的副檔名
			if (in_array($fileExt, $allowedExts)) {
                $destination = "uploads/".uniqid()."-".$fileName; //設定檔案名稱
                move_uploaded_file($fileTmpPath, $destination);  //移動檔案

                //儲存檔案資訊到資料庫
                $sql_file = "INSERT INTO uploadfiles (comment_id, file_name, file_path) VALUES (?, ?, ?)";
                $stmt = $link->prepare($sql_file);  //避免注入式攻擊
                $stmt->bind_param("iss", $comment_id, $fileName, $destination); //避免注入式攻擊
                $stmt->execute(); //執行insert
            }
        }
        echo "<script type='text/javascript'>alert('留言新增成功!'); window.location.href='main.php';</script>";
    } else {
        echo "<script type='text/javascript'>alert('留言新增失敗!');</script>";
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
    <form method="post" class="form2" enctype="multipart/form-data">
		<table align="center">
			<tr>
				<td align="center"><a href="main.php">回到主頁<br>back to main page</a></td>
			</tr>
			<tr>
				<td><input type="text" name="content" placeholder="留言" required /></td>
			</tr>
			
			<!--上傳檔案欄位，可上傳多個檔案，且限制副檔名-->
			<tr>
				<td>
					<label for="files">附加檔案: </label><br>
					<input type="file" name="files[]" id="files" multiple accept=".pdf,.doc,.docx,.jpg"> 
				</td>
			</tr>
			
			<tr>
				<td align="center"><button type="submit" name="btn-save"><strong>儲存<br>SAVE</strong></button>
			</tr>
		</table>
    </form>
    </div>
</div>

</center>
</body>
</html>