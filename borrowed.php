<?php 
	session_start();
	setcookie("fav",1,time());
	$account = $_SESSION["login"];
	require_once("readdb_php.php");
	$link = readDb();
	$sql = "SELECT B.BID, BName, AName,Borrow_D, Due_D, Renew_t, rating FROM book_t as b,author_t as a, borrow_t as t 
	WHERE t.bid = b.bid AND t.MID = '$account' AND b.AID = a.AID AND status = '未歸還'";
	$result = @mysqli_query($link,$sql);


 ?>
<!DOCTYPE html>
<html lang="UTF-8">
<head>
	<meta charset="UTF-8">
	<title>我的借閱</title>
	<link rel="stylesheet" type="text/css" href="css/general.css">

</head>
<body>
	<div id="table">
		<table>
			<thead><tr><td>書籍名稱</td><td>作者</td><td>借閱日期</td><td>到期日期</td><td>評價</td><td></td></tr></thead>
			<!-- 輸出借閱清單 -->
			<?php 
			while ($rs = @mysqli_fetch_array($result)) {
				echo "<form action='submit.php' method='POST'>
					<input name = 'bid' value='{$rs[0]}' style='display: none;'>";
				echo "<tr><td>".$rs[1]."</td><td>".$rs[2]."</td><td>".$rs[3].
				"</td><td>".$rs[4]."</td><td>".$rs[6]."</td><td style='width:15%;'><button style='width:50%' name='return' value='return'>歸還</button></td></tr></form>";
			}
			 ?>
		</table>
	</div>

	<!-- 跳轉按鈕 -->
	<div id = "back"><button onclick=location.href='index.php'>
	回首頁</button></div>
</body>
</html>