<?php 
	session_start();
	$account = $_POST["account"];
	$password = $_POST["password"];
	if($account!=""&&$password!=""){
		require_once("readdb_php.php");
		$link = $link = readDb();
		$sql = "SELECT Mname,type from member_t where MID = '$account' and Mpsw = '$password'";
		$result = @mysqli_query($link,$sql);
		if($rs=@mysqli_fetch_array($result)){
			$_SESSION["login"] = $account;
			$_SESSION["username"] = $rs[0];
			$_SESSION["type"] = $rs[1];
			header("refresh:0.05;url=index.php");
		}
	}

	 ?>


<!DOCTYPE html>
<html lang="UTF-8">
<head>
	<meta charset="UTF-8">
	<title>會員登入</title>
</head>
<body>
	<div id="loginwd" >
		請輸入帳號/密碼
		<form class="login" action="login.php" method="POST">
		帳號 : <input type="text" name="account"><br>
		密碼 : <input type="password" name="password"><br>
		<button value="submit">送出</button>	
		</form>
	</div>
</body>
</html>
<style>
	body{
		font-family: Microsoft JhengHei;
		font-size: 20px;
	}

	#loginwd{
		margin: 0px auto; 
		background-color: #ac7339; 
		width:26%;
		height: 25%; 
		margin-top: 20%;
		text-align: center; 
		vertical-align: center;
		border-radius: 10px;
		color: #fff;
		font-weight: bolder;
	}
</style>


