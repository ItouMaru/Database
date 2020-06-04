<?php 
session_start();
$name = $_SESSION["username"];
$account = $_SESSION["login"];
$type = $_SESSION["type"];
setcookie("fav",1,time());
require_once("readdb_php.php");
$link = readDb();
 ?>
<!DOCTYPE html>
<html lang="UTF-8">
<head>
	<meta charset="UTF-8">
	<title>電子書租借系統</title>
	<link rel="stylesheet" type="text/css" href="css/index.css">
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
	<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
	<link rel="icon" type="image/gif/png" href="images/library.png">
</head>
<body>
	<div id="content">
		<div>
			<div id="title">
			<h1>電子書租借系統 2020</h1>
		</div>
		<div id="right">
			<!-- 如果有登入就顯示登出按鈕和收藏 -->
				<?php 
					if($name!=""){
						echo "<span>";
						if($type == "member"){echo "會員";}else{echo "管理員";}
						echo " : ".$name." 您好</span><div id='login'><a href='?logout' style='text-decoration:none; color: white;'><img src='images/login.png' style='width: 20px; height: 20px; '>登出</a></div>";
						echo "<div class='topright'><a href='search.php?favorite=1' style='text-decoration:none; color: white;'><img src='images/bookmark.png' style='width: 20px; height: 20px;'>我的收藏</a></div>";
						echo "<div class='topright'><a href='borrowed.php' style='text-decoration:none; color: white;'><img src='images/list.png' style='width: 20px; height: 20px;'>我的書籍</a></div>";
						if($type == "administrator"){
							echo "<div class='topright'><a id='admin' data-fancybox href='#adminsearch' style='text-decoration:none; color: white;'><img src='images/head.png' style='width: 20px; height: 20px;'>權限查詢</a></div>";	
						}


					}else{
						echo "<div id='login'><a href='login.php' style='text-decoration:none; color: white;'><img src='images/login.png' style='width: 20px; height: 20px;'>登入</a></div>";
					}
					// 登出操作
					if(isset($_GET['logout'])){
						unset($_SESSION["login"]);
						unset($_SESSION["type"]);
						unset($_SESSION["username"]);
						echo "<script language='javascript'>alert('登出成功!')</script>";
						header("refresh:0.05;url=index.php");
					}
					?>
			<!-- 管理員搜尋介面 -->
			<div style="display: none;">
				<div id="adminsearch">
					<h4>借閱紀錄查詢</h4>
					<form style="height: 100%;" target="_parent" action="adminsearch.php" method="POST">
						<div id="inputarea">依
						<div id="option"><select name="option"><option value="MID">會員</option>
						<option value="BID">書籍</option></select></div>
						<div id="valueinput">會員/書籍ID:<input type="text" name="keywords" size= "40%" style="line-height:25px"></div>
						<button value="submit" >搜尋</button>
					</div>
					</form>
				</div>
			</div>
	<!-- 搜尋區塊 -->
		<div id = "searchin">
			<form class = "searchbox" action="search.php" method="GET">
				<input type="text" name="keywords" size= "40%" style="line-height:25px; margin-left:30px;" placeholder="輸入關鍵字/作者/ISBN...">
				<input id="search" type="submit" name="submit" value="搜尋">
			</form>
		</div>
		</div>
	</div>
	<!-- 顯示書籍分類 -->
		<div>
		<ul class="menu">
			<?php 
      			include "menu.php";
      		?>
		</ul>

	</div>
	<div>
	<div class="recommend">
		<span>熱門書籍TOP3</span><br><br>
		<!-- 顯示熱門書籍 -->
		<?php 
		$sql = "SELECT bo.bid, Bname FROM borrow_t bo, book_t b 
		where (SELECT COUNT(*) as c FROM borrow_t GROUP BY BID HAVING BID = bo.bid ORDER BY c DESC) 
		AND b.BID = bo.BID LIMIT 3";
		$popular = @mysqli_query($link,$sql);
		while ($po = @mysqli_fetch_array($popular)){
			echo "<div class = 'popular'><a data-fancybox data-type= 'iframe' href='detailpage.php?bid={$po[0]}'>
			<img class = 'popular' src='images/book.png'>".$po[1]."</a></div>";
		}
		 ?>
	</div>
	
		<!-- 顯示個人推薦書籍(隨機自訂閱分類中取1本最熱門的) -->
		<?php 
		if ($account != "") {
		echo "<div class='recommend'><span>個人化推薦書籍TOP3</span><span style='font-size: 14px; color: grey;'>  依照你的關注分類推薦</span><br><br>";
		$sql = "SELECT bo.bid, Bname, rand.CID FROM borrow_t bo, book_t b ,(SELECT CID FROM favcategory_t WHERE MID = '$account' ORDER BY RAND() limit 1) as rand where (SELECT COUNT(*) as c FROM borrow_t GROUP BY BID HAVING BID = bo.bid ORDER BY c DESC) AND b.CID = rand.CID AND b.BID = bo.BID LIMIT 1";
		$popular = @mysqli_query($link,$sql);
		while ($po = @mysqli_fetch_array($popular)){
			echo "<div class = 'popular'><a data-fancybox data-type= 'iframe' href='detailpage.php?bid={$po[0]}'>
			<img class = 'popular' src='images/book.png'>".$po[1]."</a></div>";
		}
		}
		 ?>
	</div>
	<!-- 興趣分類檢視 -->
	<?php if ($name!="") {
		echo "<div class='field'>
		<button style='width:100px;' onclick=location.href='search.php?";
		$sql="SELECT CID FROM favcategory_t WHERE MID = '$account'";
		$result = @mysqli_query($link,$sql);
		while ($rs = @mysqli_fetch_array($result)) {
			echo "categorys[]=".$rs[0]."&";
		}
		echo "'>興趣分類</button></div>";
	} ?>
	</div>

</body>
</html>

<style>
	.drop:hover .subscribe {
	<?php if ($name!="")
		echo "display: block;";
	?>
	
}


</style>