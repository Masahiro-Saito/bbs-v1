<?php
// クラス宣言
require_once("./class/DbPdo.php");
require_once("./class/ThreadController.php");

// インスタンス
$thrdCtrl = new ThreadController();
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>掲示板</title>

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="./css/bbs.css">

<script type="text/javascript">
<!--
// 入力チェック
function validate(){

	var error = "";

	// スレッド名
	if(document.addThreadForm.title.value == "") {
		error += "「スレッド名」が未入力です。\r\n";
	}
	// 名前
	if(document.addThreadForm.writer.value == "") {
		error += "「名前」が未入力です。\r\n";
	}
	// 内容
	if(document.addThreadForm.writetext.value == "") {
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
<?php
		// スレッド名表示
		foreach($thrdCtrl->getThreadList() as $list) {
			echo "<br>";
			echo "<div class='thread_data'>";
			echo "<a href=./thread.php?id=$list[id]&title=$list[title] />" . $list['title'] . "</a>";
			echo "<br>";
			echo "<div class='thread_data_contents'>";

			// スレッド内容表示
			$no = 1; // 書き込み番号
			foreach($thrdCtrl->getThreadContents($list['id'], 5) as $cont) {
				echo $no++ . '：' . $cont['writer'] . ' ' . $cont['writetime'];
					echo "<br>";
					// nl2br（改行文字を<br>タグに変換）
					echo nl2br($cont['writetext']);
					echo "<br><br>";
				}
			echo "</div></div>";
		}
?>
		<!-- スレッド作成 -->
		<br>
		<div class="thread_make">
			<span>新規スレッド作成<span>
			<div class="thread_make_contents">
				<form name="addThreadForm"  method="post" action="./class/MakeController.php"  onSubmit="return validate()">
					スレッド名：<br>
					<input name="title" type="text" size="60" /><br>
					名前：<br>
					<input name="writer" type="text" size="60" /><br>
					内容：<br>
					<textarea name="writetext" col="40" rows="5" ></textarea><br>
					<input type="submit" value="実行"/>
					<!-- hidden -->
					<input name="action" type="hidden" value="addThread" />
					<input name="path" type="hidden" value="index" />
				</form>
			</div>
		</div>
	</div>
</body>
</html>
