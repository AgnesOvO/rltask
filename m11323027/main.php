<?php
  session_start();
  $inactive = 900; //設定過期時間，15min*60=900sec

  //檢查是否已有$_SESSION['last_activity']
  if (isset($_SESSION['last_activity'])) {
	  //檢查最後活動時間是否逾時
      if ((time() - $_SESSION['last_activity']) > $inactive) {
        session_unset();
        session_destroy();
		//提醒使用者連線逾時，重定向至首頁
        echo "<script type='text/javascript'>alert('連線逾時!'); window.location.href = 'login.php';</script>";
        exit();
      }
  }
  $_SESSION['last_activity'] = time(); //更新最後活動時間


  //$_SESSION["login_user"]有資料，代表已正常登入
  if (!isset($_SESSION["login_user"])) {
      header("Location: main.php");
      exit();
  }

  include_once 'dbconfig.php';
  if(isset($_GET['delete_id']))
  {
      $delete_id = mysqli_real_escape_string($link, $_GET['delete_id']);
      $username = $_SESSION["login_user"];
  
      $sql_query = "DELETE FROM comments WHERE id=$delete_id AND nickname=(SELECT nickname FROM users WHERE username='$username')";
      if(mysqli_query($link, $sql_query)){
          echo "<script type='text/javascript'>alert('留言已刪除!'); window.location.href='main.php';</script>";
      }
      else{
          echo "<script type='text/javascript'>alert('留言刪除失敗'); window.location.href='main.php';</script>";
      }
  }
?>   

<!DOCTYPE html>
<html>
<title>智慧型資料庫系統實驗室作業</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inconsolata">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script>
function edt_id(id)
{
	if(confirm('確定要更新資料嗎? Sure to edit?'))
	{
		window.location.href='edit_data.php?edit_id='+id;
	}
}
function delete_id(id)
{
	if(confirm('確定要刪除資料嗎? Sure to Delete?'))
	{
		window.location.href='main.php?delete_id='+id;
	}
}
function confirm_logout()
{
	if(confirm('確定要登出嗎? Sure to Logout?'))
	{
		window.location.href='logout.php';
	}
}

</script>

<!--登入成功-->
<script type="text/javascript">
    window.onload = function() {
        //檢查是否有登入成功的訊息
        <?php if (isset($_SESSION['login_success'])) { ?>
            alert('登入成功!');
            <?php 
			unset($_SESSION['login_success']); //清除標記 
			?>
        <?php } ?>
    }
</script>

<body>
<style>
body, html {
  height: 100%;
  font-family: "Inconsolata", sans-serif;
}

.bgimg {
  background-position: center;
  background-size: cover;
  background-image: url("cover.jpg");
  min-height: 75%;
}
.ms{

}
.contact {
  display: none;
}
</style>
<!-- Links (sit on top) -->
<div class="w3-top">
  <div class="w3-row w3-padding w3-black">
	
	<h1>Welcome, <?php echo htmlspecialchars($_SESSION["login_user"]); ?>!</h1>
	
    <div class="w3-col s3">
      <button onclick="confirm_logout()">登出 Logout</button>
    </div>

  </div>
</div>

<!-- Header with image -->
<header class="bgimg w3-display-container w3-grayscale-min" id="home">
</header>

<!-- Add a background color and large text to the whole page -->
<div class="w3-sand w3-grayscale w3-large">

<!-- Menu Container-->
  <div class="w3-content">
    <div class="w3-container">
      <h5 class="w3-center w3-padding-48"><span class="w3-tag w3-wide">留言板<br>BULLETIN BOARD</span></h5>
	  
	  <center>
	    <tr>
		  <th colspan="2"><a href="add_data.php"><font color="#FF0000">新增留言 Add new comment!</font></a></th>
	    </tr>
	  </center>
    </div>
	
	<br></br>
	
    <table class="table table-striped">
	<tr>
		<th><h3>nickname</h3></th>
		<th><h3>content</h3></th>
		<th><h3>time</h3></th> 
		<th colspan="2"><h2>Operations</h2></th>
    </tr>
    <?php
	$sql_query="SELECT * FROM comments";
	$result_set=mysqli_query($link,$sql_query);
	if(mysqli_num_rows($result_set)>0)
	{
        while($row=mysqli_fetch_row($result_set))
		{
		?>
            <tr>
            <td><h4><?php echo $row[1]; ?></h4></td>
            <td><h4><?php echo $row[2]; ?></h4></td>
            <td><h4><?php echo $row[3]; ?></h4></td>
            <td alt="center"><a href="javascript:edt_id('<?php echo $row[0]; ?>')"><img src="t_edit.png" alt="EDIT" /></a></td>
            <td alt="center"><a href="javascript:delete_id('<?php echo $row[0]; ?>')"><img src="t_drop.png" alt="DELETE" /></a></td>
            </tr>
        <?php
		}
	}
	else
	{
		?>
        <tr>
        <td colspan="5">查無資料! No data found!</td>
        </tr>
        <?php
	}
	?>
	</div>
    </table>
</div>

<!-- Footer -->
<footer class="w3-center w3-light-grey w3-padding-48 w3-large">
  <p></p>
</footer>

</body>
</html>