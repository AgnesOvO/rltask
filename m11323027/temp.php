<script type="text/javascript">

    window.onload = function() {
        //檢查登入成功的session
        <?php if (isset($_SESSION['login_success']) && $_SESSION['login_success']) 
		{ 
		?>
            alert('登入成功!');
            <?php 
			unset($_SESSION['login_success']); //清除成功訊息 
			?>
        <?php 
	}
	?>
    }
</script>