<?php 
	/* Report all errors except E_NOTICE */

	error_reporting(E_ALL^E_NOTICE);
	session_start();
	$account = $_SESSION["login"];
	$type = $_POST["option"];
	$key = $_POST["keywords"];
	$sql = "SELECT Bname, Borrow_D, Due_D, status From borrow_t bo ,book_t b WHERE b.BID = bo.BID AND bo.$type = '$key'";
	require_once("readdb_php.php");
	$link = readDb();
	$result = @mysqli_query($link,$sql);
	@$total_records=mysqli_num_rows($result);
	echo "<h2>搜尋";
	if($type == "MID"){
		echo "用戶 ";
	}else{echo "書籍 ";}
	echo $key." 之借閱紀錄</h2>";

 ?>
<!DOCTYPE html>
<html lang="UTF-8">
<head>
	<meta charset="UTF-8">
	<title>管理員權限查詢</title>
	<link rel="stylesheet" type="text/css" href="css/general.css">
</head>
<body>
	<div id="content">
		搜尋筆數:<?php echo $total_records." 筆" ?>
		<table>
			<thead><tr><td>書名</td><td>借閱日期</td><td>歸還日期</td><td>狀態</td></tr></thead>
			<?php 
				while ($rs = @mysqli_fetch_array($result)) {
					echo "<tr><td>".$rs[0]."</td><td>".$rs[1]."</td><td>".$rs[2]."</td><td>".$rs[3].
					"</td></tr>";
				}
			 ?>
		</table>
	</div>

	<!-- 跳轉按鈕 -->
	<div id = "back"><button onclick=location.href='index.php'>
	回首頁</button></div>
</body>
</html>