<?php 
	session_start();
	$account = $_SESSION["login"];
	// 一般搜尋
	$sqlo = "SELECT DISTINCT b.BID,BName, Aname from book_t as b, author_t as a  ";    
	// 參數s'
	if(isset($_GET["categorys"])){
		foreach ((array)$_GET["categorys"] as $k => $ckey) {
			$categorys[] = $ckey;
		}  
	}
	$key = $_GET["keywords"];
	$publishdate = array($_GET["rangebegin"],$_GET["rangend"]);
	// 預設category的sql
	$sqlc = " CID IN ( ";
	if(isset($categorys)){
		foreach ($categorys as $ckey) {
			$sqlc .= "'".$ckey."',";
		}
	}
	$sqlc = substr($sqlc,0,-1).") ";
	// 預設keywords的sql
	$sqlk ="(BName LIKE '%$key%' AND b.AID = a.AID) OR 
	(Aname LIKE '%$key%' AND a.AID = b.AID) OR (b.bid = '$key') ";
	// 預設publishdate的sql
	$sqlp = "(Publishdate BETWEEN '$publishdate[0]' AND '$publishdate[1]') ";
	if(isset($_GET["categorys"])){		
		$sql = $sqlo."WHERE (".$sqlc;
		if(isset($_GET["keywords"])){
			$sql .= "AND ".$sqlk.")";
			if($publishdate[0] != ""){
				$sql .=" AND ".$sqlp;
			}
		}
	}else if(isset($_GET["keywords"])){
		$sql = $sqlo."WHERE (".$sqlk.")";
		if($publishdate[0] != ""){
			$sql .=" AND ".$sqlp;
		}
	}else{
		$sql = $sqlo."WHERE (".$sqlp;
		if(isset($_GET["categorys"])){
			$sql .= "AND ".$sqlc.")";
		}
	}

	$sql.=" and (a.AID = b.AID) ";
	// 排序
	if (isset($_GET["orders"])) {
		$sql .= " ORDER BY ";
		foreach ($_GET["orders"] as $ordkey) {
			$sql .= $ordkey." DESC, ";			
		}
		$sql = substr($sql, 0,-2);
	}

	$isfav = $_GET["favorite"];//收藏清單
	if(isset($isfav)){
		setcookie("fav",1);
		$sql = $sqlo.",member_t as m, favbook_t as fav 
		WHERE fav.BID = b.BID AND A.AID = B.AID
		AND fav.MID = '$account'";
	}

	// 執行query
	require_once("readdb_php.php");
	$link = readDb();
	$result = @mysqli_query($link,$sql);
	@$total_records=mysqli_num_rows($result);
 ?>
<!DOCTYPE html>
<html lang="utf-8">
<head>
	<meta charset="UTF-8">
	<title><?php if(isset($isfav)){echo "我的收藏";}else{echo "搜尋結果";}?></title>
	<link rel="stylesheet" type="text/css" href="css/general.css">
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
	<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>	

</head>
<body>

	<div id = "table">
	搜尋結果: <?php echo $total_records." 筆" ?>
	<table >
		<thead><tr><td>書籍名稱</td><td>作者</td></tr></thead>
	<!-- 輸出搜尋結果 -->
	<?php 
		while ($rs = @mysqli_fetch_row($result)){
			echo "<tr><td class = 'left'><a class = 'detailpage' data-fancybox data-type= 'iframe' href = 'detailpage.php?bid={$rs[0]}'>".$rs[1]."</a></td><td class='right'>".$rs[2]."</td></tr>";

		}
	 ?>
	</table>

</div>
	<!-- 進階搜尋 -->
	<div <?php if(isset($isfav)){echo "style='display:none;'";}?>>
		沒有找到?試試<button id="show" onclick="change()">進階搜尋</button>           
	</div> 
	<script type="text/javascript">
		var $isopen=false;
		function change(){
			if($isopen){
				document.getElementById('advancedsearch').style.display='none';
				document.getElementById('show').innerText = '進階搜尋';
				$isopen = false;
			}else{
				document.getElementById('advancedsearch').style.display='block';
				document.getElementById('show').innerText = '關閉';
				$isopen = true;			
			}
		}
	</script>
	<div id="advancedsearch" style="display: none;">
		<form>
			<!-- 再傳一次關鍵詞 -->
			<?php
				if($key != ""){ echo "<input name = 'keywords' value='{$key}' style='display: none;'>";}
			?>
			<table>
				<tr><td>排序依</td>
					<td>
					<div class="label"><input id="BName" type="checkbox" name="orders[]" value="BName">
						<label for="BName">書名</label></div>
					<div class="label"><input id="AID" type="checkbox" name="orders[]" value="AID">
						<label for="AID">作者</label></div>
					<div class="label"><input id="Publishdate" type="checkbox" name="orders[]" value="Publishdate">
						<label for="Publishdate">出版日期</label></div>					
					<div class="label"><input id="rating" type="checkbox" name="orders[]" value="rating">
						<label for="rating">評價</label></div>
				</td></tr>
				<tr><td>分類</td><td>
				<?php  
					$sql = "SELECT CID, Cname from category_t";
					$result = mysqli_query($link,$sql);
					while($rs=mysqli_fetch_row($result)){
						echo "<div class='label'><input id={$rs[0]} type='checkbox' name='categorys[]' value = {$rs[0]}";
						// 使用前一次存的category陣列傳預設值給下一次搜索
						if (isset($categorys)) {
							for($i = 0;$i < count($categorys);$i++){
								if ($rs[0] == $categorys[$i]){
								echo " checked ";
								}
							}						
						}
						echo "><label for='{$rs[0]}'>".$rs[1]."</label></div>";
					}
				?>
				</td></tr>
				<tr><td>出版年份</td><td>
					<div class="select">
					<select name="rangebegin">
					<option value="" selected>-</option>
					<?php
						for($i=2005;$i<=2020;$i++)
						echo "<option values='{$i}'>".$i."</option>";
					  ?>
					</select>
					</div>  &nbsp~&nbsp  
					<div class="select">
					<select name="rangend">
						<option value="" selected>-</option>
						<?php
						for($i=2005;$i<=2020;$i++)
						echo "<option values='{$i}'>".$i."</option>";
					  ?>
					</select>
					</div>
				</td></tr>
			</table>
 
			<button value="submit">搜尋</button>
		</form>
	</div>
	<!-- 跳轉按鈕 -->
	<div id = "back"><button onclick=location.href='index.php'>
	回首頁</button></div>

</body>
</html>
