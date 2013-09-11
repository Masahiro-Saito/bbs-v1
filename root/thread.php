<?php
// パラメータ取得
$id = $_GET['id'];
$title = $_GET['title'];

// クラス宣言
require_once("./class/DbPdo.php");
require_once("./class/ThreadController.php");

// インスタンス
$thrdCtrl = new ThreadController();
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $title; ?></title>

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="./css/bbs.css">

<script type="text/javascript">
<!--
// 入力チェック
function validate(){

	var error = "";

	// 名前
	if(document.addContentsForm.writer.value == "") {
		error += "「名前」が未入力です。\r\n";
	}
	// 内容
	if(document.addContentsForm.writetext.value == "") {
		error += "「内容」が未入力です。\r\n";
	}

	// エラーメッセージを表示して処理を中止
	if(error != "") {
		window.alert(error);
		return false;
	}
}
// -->
</script>
</head>
<body>
	<div align="center">
		<!-- スレッド一覧 -->
		<div class="thread_list">
			<span>スレッド一覧<span>
			<div class="thread_list_contents">
<?php
				foreach($thrdCtrl->getThreadList() as $list) {
					echo "<a href=./thread.php?id=$list[id]&title=$list[title] />" . $list['title'] . "</a>";
					echo "<br>";
				}
?>
			</div>
		</div>
		<!-- スレッド内容 -->
		<br>
		<div class="thread_data">
<?php
			// スレッド名表示
			echo "<a href=./thread.php?id=$id&title=$title />" . $title . "</a>";
?>
			<div class="thread_data_contents">
<?php
			// スレッド内容表示
			$no = 1; // 書き込み番号
			foreach($thrdCtrl->getThreadContents($id, 0) as $cont) {
				echo $no++ . '：' . $cont['writer'] . ' ' . $cont['writetime'];
					echo "<br>";
					echo $cont['writetext'];
					echo "<br><br>";
			}
?>
			</div>
		</div>
		<!-- 書き込む -->
		<br>
		<div class="thread_make">
			<span>書き込む<span>
			<div class="thread_make_contents">
				<form name="addContentsForm"  method="post" action="./class/MakeController.php"  onSubmit="return validate()">
					名前：<br>
					<input name="writer" type="text" size="60" /><br>
					内容：<br>
					<textarea name="writetext" col="40" rows="5" ></textarea><br>
					<input type="submit" value="実行"/>
					<!-- hidden -->
					<input name="action" type="hidden" value="addContents" />
					<input name="path" type="hidden" value="thread" />
					<input name="id" type="hidden" value=<?php echo $id; ?> />
					<input name="title" type="hidden" value=<?php echo $title; ?> />
				</form>
			</div>
		</div>
		<!-- スレッド一覧へ戻る -->
		<br>
		<a href="./index.php">一覧へ戻る</a>
	</div>
</body>
</html>
