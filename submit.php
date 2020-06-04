

<script type="text/javascript" src="jquery.fancybox.js?v=2.1.2"></script>
<link rel="stylesheet" type="text/css" href="jquery.fancybox.css?v=2.1.2" media="screen" />
<script language='javascript'>
	function open(){
		document.getElementById('oprating').onclick();
	}
			</script>
<?php 
	setcookie("fav",1,time());
	session_start();
	require_once("readdb_php.php");
	$link = readDb();
	$account = $_SESSION["login"];
	date_default_timezone_set('Asia/Taipei');   //設定時區
	$date = date("Y-m-d");
	$dueday = date("Y-m-d", strtotime($date."+15days"));
	$bid = $_GET["bid"];


	// 借閱
	if (isset($_POST["borrow"])) {
		if($account != ""){
			$sql="UPDATE borrow_t SET Renew_t = (SELECT Renew_t FROM borrow_t where (select Mbq from member_t where MID = '$account') > 0 AND BID = '$bid' AND MID = '$account' ORDER BY Due_D DESC LIMIT 1) + 1, status = '未歸還',Due_D = '$dueday' WHERE BID = '$bid' AND MID = '$account' AND Borrow_D = (SELECT Borrow_D FROM borrow_t ORDER BY Borrow_D DESC LIMIT 1) AND (SELECT Due_D FROM borrow_t where BID = '$bid' AND MID = '$account' ORDER by Due_D DESC LIMIT 1) > '$date';
				insert into borrow_t SELECT '$bid','$account','$date','$dueday','0','NULL','未歸還' 
					WHERE (select Mbq from member_t where MID = '$account') > 0 AND NOT EXISTS (SELECT Due_D FROM borrow_t where BID = '$bid' AND MID = '$account' ORDER by Due_D DESC LIMIT 1) > '$date';";
	
			if($result = @mysqli_multi_query($link,$sql)){
				
				$sqlk="update member_t set mbq = IF((SELECT Renew_t FROM borrow_t 
				where BID = '$bid' AND MID = '$account') = 0,(select Mbq from member_t where MID = '$account') -1,
				(select Mbq from member_t where MID = '$account')) WHERE MID = '$account'";
				$up = @mysqli_query($link,$sqlk);
				$test = @mysqli_fetch_array($up);
				if(!$test){
					echo "<script language='javascript'>alert('借閱成功!')</script>";
				}
			}else{
				echo "<script language='javascript'>alert('您的借閱額度不足!')</script>";
			}
			echo "<script language='javascript'>history.back();
			</script>";
		}else{
			echo "<script language='javascript'>alert('您沒有登入!');
			parent.$.fancybox.close();
			</script>";
			header("refresh:0.05;url=login.php");
		}
	}
	// 收藏&取消收藏
	if (isset($_POST["fav"])) {
		if ($account!="") {
		if ($_POST["fav"] == "收藏") {
			$sql = "INSERT INTO favbook_t VALUES ('$bid','$account')";
			$alert = "已新增至收藏清單!";
		}else{
			$sql = "DELETE FROM favbook_t where BID = '$bid' AND MID = '$account'";
			$alert = "已取消收藏!";
		}
		if(@mysqli_query($link,$sql)){
			echo "<script language='javascript'>alert('".$alert."');history.back();
			</script>";
		}else{
			echo "<script language='javascript'>alert('您已收藏過該書籍!');history.back();
			</script>";			
		}
		}else{
			echo "<script language='javascript'>alert('您沒有登入!');
			parent.$.fancybox.close();
			</script>";
			header("refresh:0.05;url=login.php");			
		}
		
	}


	// 歸還
	if(isset($_POST['return'])){
		$bid=$_POST["bid"];
		$sql = "Update borrow_t SET status = '已歸還' WHERE bid = '$bid' AND MID = '$account';
				Update member_t SET mbq = (SELECT mbq from member_t WHERE MID = '$account') + 1 WHERE MID = '$account';";
		if(@mysqli_multi_query($link,$sql)){
			header("refresh:0.05;url=submit.php?bid=$bid&rating=1");	
		}

	}

	// 評分
	if (isset($_POST['rating'])) {
		$bid=$_GET["bid"];
		$rating = $_POST['rating'];
		$sql="Update borrow_t SET rating = '$rating' WHERE BID = '$bid' AND MID = '$account'";
		$result = @mysqli_query($link,$sql);
		if($result){
			echo "<script language='javascript'>
			alert('評分成功!');
			</script>";	
			header("refresh:0.05;url=borrowed.php");
		}
	}
	// 關注分類
	if(isset($_GET["subscribe"])){
		$category = $_GET["category"];
		$sql = "INSERT INTO favcategory_t VALUES ('$category','$account')";
		if(@mysqli_query($link,$sql)){
			echo "<script language='javascript'>
			alert('關注成功! 可至首頁右方查看');history.back();
			</script>";	
		}
	}
 ?>

<!-- 評分表單 -->
<div <?php if(isset($_GET['rating'])){$bid=$_GET["bid"];}else{ echo "style='display: none;'";} ?>>
	<div id="rating">
	<h4>感謝您的借閱!</h4>
	<h5>在此留下您的評價:</h5>
		<form style="height: 100%;" action="submit.php?bid=<?php echo $bid ?>" method="POST">
			<div id="option"><select name="rating"><option value="-" selected>-</option>
				<?php 
					for ($i=1; $i <= 5; $i++) { 
					echo "<option value='$i'>".$i."</option>";
					}
				 ?>
			</select></div>
			<button value="submit" >確認</button>
		</div>
		</form>
	</div>
</div>

<style>
	#rating{
		margin: 0px auto; 
		background-color: #ffd480; 
		width:26%;
		height: 25%; 
		margin-top: 20%;
		text-align: center; 
		vertical-align: center;
		border-radius: 10px;
		color: #fff;
	}

	select{
  background-color: #fff;
  width: 30%;
  flex:1;
  color: black;
  cursor: pointer;
  border-style:none; 
  font-size: 18px;
  text-align: right;
  line-height: 1.75px;
  appearance:none;
  padding: 0px 9px;
  border: 2px solid #4b3c37;
}

	body{
		font-family: Microsoft JhengHei,Comfortaa, cursive; 
		background-color: #f2f2f2;
	}

	button{
	border: 2px solid #4b3c37;
	background-color :black;
	color : white;
	width :20%;
	height: 30px;
  	text-align: center;
  	text-decoration: none;
 	display: block;
 	font-size: 20px;
 	margin: 0px auto;
 	margin-top: 10px;
 	padding-bottom: 10px;
 	font-family: Microsoft JhengHei;
}
</style>