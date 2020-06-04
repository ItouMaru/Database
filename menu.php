<?php 
	require_once("readdb_php.php");
	$link = readDb();
	$sql= "SELECT CID,Cname from category_t";
	$result = @mysqli_query($link,$sql);
	while($rs = mysqli_fetch_row($result)){
		echo "<div class = 'drop'><li class='category'><a  href='search.php?categorys={$rs[0]}'>".$rs[1]."</a></li>
		<div class='subscribe'><button onclick=location.href='submit.php?category=".$rs[0]."&subscribe=1'>關注</button></div>
		</div>";
	}		
 ?>