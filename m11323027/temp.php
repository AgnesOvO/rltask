<?php
session_start(); //啟動SESSION

//檢查是否有正常登入 BY檢查裡面有沒有Login_user
if(!isset($_SESSION["login_user"]))
{
    echo "<script type='text/javascript'>alert('注意！請登入再使用')</script>";
    header("location:index.php");
}

//查詢此留言是否屬於目前登入的使用者
include_once 'dbconfig.php'; //執行資料庫連線
if(isset($_GET['edit_id']))
{
    $edit_id = mysqli_real_escape_string($link, $_GET['edit_id']); //避免SQL Injection
    $username = $_SESSION["login_user"]; //取得目前登入的使用者名稱

	//選取範圍
    $sql_query = "SELECT * FROM comments WHERE id=$edit_id AND nickname=(SELECT nickname FROM users WHERE username='$username')";
    $result_set = mysqli_query($link, $sql_query);

	//如果查詢結果有資料返回，則取出該資料
    if(mysqli_num_rows($result_set) > 0) {
        $fetched_row = mysqli_fetch_array($result_set);
    }
	//顯示錯誤訊息並跳轉到主頁
	else {
        echo "<script type='text/javascript'>alert('無法編輯這條留言!'); window.location.href='main.php';</script>";
        exit();
    }
}

//更新留言
if (isset($_POST['btn-update'])) {
    $content = $_POST['content']; //取得留言
    $content_xx = mysqli_real_escape_string($link, $content); //避免SQL Injection

    //更新留言
    $sql_query = "UPDATE comments SET content='$content_xx' WHERE id=".$_GET['edit_id']." AND nickname=(SELECT nickname FROM users WHERE username='".$_SESSION["login_user"]."')";

    if (mysqli_query($link, $sql_query)) {
		
		if (mysqli_query($link, $sql_query)) {
        // 處理檔案刪除
        if (isset($_POST['delete_files'])) {
            $files_to_delete = $_POST['delete_files'];
            foreach ($files_to_delete as $file_id) {
                $sql_file = "SELECT file_path FROM uploadfiles WHERE id = ?";
                $stmt = $link->prepare($sql_file);
                $stmt->bind_param("i", $file_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $file = $result->fetch_assoc();
                unlink($file['file_path']); // 刪除伺服器上的檔案
                
                $sql_delete = "DELETE FROM uploadfiles WHERE id = ?";
                $stmt = $link->prepare($sql_delete);
                $stmt->bind_param("i", $file_id);
                $stmt->execute();
            }
        }

		
        // 處理上傳的檔案
        if (!empty($_FILES['files']['name'][0])) {
            $files = $_FILES['files'];
            $allowedExts = ['pdf', 'doc', 'docx', 'jpg'];

            for ($i = 0; $i < count($files['name']); $i++) {
                $fileName = $files['name'][$i];
                $fileTmpPath = $files['tmp_name'][$i];
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

                if (in_array($fileExt, $allowedExts)) {
                    $destination = "uploads/" . uniqid() . "-" . $fileName;
                    move_uploaded_file($fileTmpPath, $destination);

                    // 儲存檔案資訊到資料庫
                    $sql_file = "INSERT INTO uploadfiles (comment_id, file_name, file_path, uploaded_time) VALUES (?, ?, ?, NOW())";
                    $stmt = $link->prepare($sql_file);
                    $stmt->bind_param("iss", $edit_id, $fileName, $destination);
                    $stmt->execute();
                }
            }
        }
        ?>
        <script type="text/javascript">
        alert('留言更新成功!');
        window.location.href='main.php';
        </script>
        <?php
    } else {
        ?>
        <script type="text/javascript">
        alert('留言更新失敗');
        window.location.href='main.php';
        </script>
        <?php
    }
  }
}

// 顯示已上傳的檔案
function displayUploadedFiles($comment_id, $link) {
    $sql_files = "SELECT id, file_name, file_path FROM uploadfiles WHERE comment_id = ?";
    $stmt = $link->prepare($sql_files);
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $result_files = $stmt->get_result();
    
    if ($result_files->num_rows > 0) {
        while ($file = $result_files->fetch_assoc()) {
            echo "<input type='checkbox' name='delete_files[]' value='" . $file['id'] . "'> " . "<a href='" . $file['file_path'] . "' download>" . $file['file_name'] . "</a><br>";
        }
    } else {
        echo "無附加檔案";
    }
}

//取消更新留言
if(isset($_POST['btn-cancel']))
{
    header("Location: main.php");
}
?>

<style>
.hd{
	background-color: #71A3AF;
}
.form3{
	width: 800px;
	height: 30px;
}
</style>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>智慧型資料庫系統實驗室作業 - 修改留言</title>
<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<center>

<div id="header">
	<div id="content" class="hd">
    <label>修改留言</label>
    </div>
</div>

<div id="body">
	<div id="content">
    <form method="post" class="form3">
    <table align="center">

		<tr>
			<td><input type="text" name="content" placeholder="留言" value="<?php echo $fetched_row['content']; ?>" required /></td>
		</tr>

        <tr>
            <td>
                <label for="files">附加檔案: </label><br>
                <input type="file" name="files[]" id="files" multiple accept=".pdf,.doc,.docx,.jpg">
            </td>
        </tr>
        <tr>
            <td>
                <?php displayUploadedFiles($edit_id, $link); ?>
            </td>
        </tr>

		<tr>
			<td>
				<button type="submit" name="btn-update"><strong>更新<br>UPDATE</strong></button>
				<button type="submit" name="btn-cancel"><strong>取消<br>Cancel</strong></button>
			</td>
		</tr>
    </table>
    </form>
    </div>
</div>

</center>
</body>
</html>