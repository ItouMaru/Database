<?php 
	session_start();
	require_once("readdb_php.php");
	$link = readDb();
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>書籍詳情</title>
</head>
<body>
	

	<div style="width:auto;height:auto;overflow: auto;position:relative;" id="detailpage">
			<table>
					<tbody><tr><td class="thumbnail"><img src="images/book.png" style="width: 60%; height: 30%;"></td><td class="bookinfo"><ul>
						<?php 
						$bid = $_GET["bid"];
						$sql = "SELECT BName,BID,Aname,(CASE WHEN book_t.TID IS NULL THEN Tname = NULL ELSE Tname END) AS Tname ,PublishDate,Pname,Cname,Intro FROM `book_t` JOIN `publisher_t`,`category_t`,`translator_t`,`author_t` WHERE book_t.CID = category_t.CID AND publisher_t.PID = book_t.PID AND ( book_t.TID IS NULL OR book_t.TID = translator_t.TID) and author_t.AID = book_t.AID 
							and BID = '$bid'";
						$detail = @mysqli_query($link,$sql);
						$detailrs= @mysqli_fetch_row($detail);
						$info = array('','ISBN','作者','譯者','出版年','出版社','分類');
						echo "<span><b>".$detailrs[0]."</b><span><hr>";
						for($i = 1;$i<7;$i++){
							echo "<li>$info[$i] : ".$detailrs[$i]."</li><br>";
						}
						 ?>
				</ul></td></tr>
			<tr>
				<td >書籍簡介:</td>
					<td id="intro">
					<?php 
						echo $detailrs[7];
					 ?>
					</td>
			</tr>
			</tbody>
			</table>
			<div>
				<form target="_parent" action="submit.php?bid=<?php echo $bid ?>" method="POST">
				<div><input class="left" type="submit" name="borrow" value="借閱">
					
				</div>
				
				<div><input class="right" type="submit" name="fav" value="<?php if(isset($_COOKIE["fav"]))
				{echo "取消收藏";}else{echo "收藏";} ?>"></div>
				</form>
			</div>
	</div>
</body>
</html>
<style type="text/css">
	body{
	font-family: Microsoft JhengHei,Comfortaa, cursive; 
}
table{
  width:80%;
  margin: auto;
  font-size: 20px;
}

  tbody{
    text-align: left;
    background-color: #F8F8FF;
}
    th{
      background: #eee;
    }
   td{
   	width : 30%;
   }
   td.bookinfo{
   	width: 70%;
   	padding: 0px;
   	padding-top: 0px;
   }
   td.thumbnail{
   	width: 30%;
   	margin-right: 0px;
   	vertical-align: middle;
   	padding-bottom: 20%;
   	padding-top: 20%;
   	padding-right: 10px;
   }

ul{
	list-style-type:none;
	padding-left:10px; 
}
hr {
    border: 0;
    height: 1px;
    background-image: linear-gradient(to right, rgba(0,0,0,0.75), rgba(0,0,0,0));
}
input{
	border-style: none;
	background-color :black;
	color : white;
	width :10%;
	height: 30px;
  	text-decoration: none;
 	display: flex;
 	text-align: center;
 	font-size: 20px;
 	margin: 0px auto;
 	margin-top: 10px;
 	padding-bottom: 10px;
 	font-family: Microsoft JhengHei;
}
input:hover{
	background-color: #808080;
	color : #f2f2f2;
}

input.left{
	align-self: left;
	float: left;
	margin-left: 40%;
}
input.right{
	align-self: right;
	float: left;
	margin-left: 25px;
}

div#back{
	display: block;
	margin: 0px auto;
}

button{
 	display: flex;	
}

</style>
 
