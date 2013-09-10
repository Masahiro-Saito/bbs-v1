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
					echo $cont['writetext'];
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
				<form method="post" action="./class/ThreadList.php?action=methodName">
					スレッド名：<br>
					<input type="text" size="60" /><br>
					名前：<br>
					<input type="text" size="60" /><br>
					内容：<br>
					<textarea col="40" rows="5" ></textarea><br>
					<input type="submit" value="実行"/>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
