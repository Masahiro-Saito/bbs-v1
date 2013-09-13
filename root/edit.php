<?php
// パラメータ取得
$id = $_GET['id'];
$title = $_GET['title'];

// クラス宣言
require_once("./class/DbPdo.php");
require_once("./class/ThreadController.php");

// インスタンス
$thrdCtrl = new ThreadController();
// 書き込みを全件取得
$cont = $thrdCtrl->getThreadContents($id, 0);
// 書き込み内容削除ボタンの制御項目作成
$contsDelBtn = "disabled";
if (count($cont) > 1) {
	$contsDelBtn = "";
}

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>編集</title>

<!-- CSS -->
<link rel="stylesheet" type="text/css" href="./css/bbs.css">

<script type="text/javascript">
<!--
function setCont(selectBox) {
	// セレクトボックスから値を配列用の番号を取得
	var i = selectBox.value - 1;

	// PHPから値を取得
	var cont = <?php echo json_encode($cont); ?>;
	// Formに値を設定
	var form = document.editContentsForm;
	form.id.value = cont[i]['id'];
	form.writer.value = cont[i]['writer'];
	form.writetext.value = cont[i]['writetext'];
}

// 入力チェック(スレッド編集)
function validate_thrd(form, btn){

	// 各項目に必要な値を設定
	form.action.value = btn.name;

	// スレッド名変更の場合
	if(btn.name == "editThread") {
		var error = "";

		// スレッド名
		if(form.title.value == "") {
			error += "「スレッド名」が未入力です。\r\n";
		}

		// エラーメッセージを表示して処理を中止
		if(error != "") {
			window.alert(error);
			return false;
		}

		// 処理を実行
		form.submit();
	}

	// スレッド削除の場合
	if(btn.name == "deleteThread") {
		if(window.confirm('削除してもよろしいですか？')) {
			// 処理を実行
			form.submit();
		}
	}
}


//入力チェック(書き込み内容編集)
function validate_cont(form, btn){
	// 各項目に必要な値を設定
	form.action.value = btn.name;

	// 書き込み内容変更の場合
	if(btn.name == "editContents") {
		var error = "";

		// 名前
		if(form.writer.value == "") {
			error += "「名前」が未入力です。\r\n";
		}

		// 内容
		if(form.writetext.value == "") {
			error += "「内容」が未入力です。\r\n";
		}

		// エラーメッセージを表示して処理を中止
		if(error != "") {
			window.alert(error);
			return false;
		}

		// 処理を実行
		form.submit();
	}

	// 書き込み内容削除の場合
	if(btn.name == "deleteContents") {
		// 削除確認
		if(window.confirm('削除してもよろしいですか？')) {
			// 処理を実行
			form.submit();
		}
	}
}
// -->
</script>

</head>
<body>
	<div align="center">
		<div class="thread_make">
			<span><?php echo $title; ?>編集</span>
			<div class="thread_make_contents">
				<!-- スレッド編集 -->
				<form name="editThreadForm"  method="post" action="./class/MakeController.php" >
					スレッド名：<br>
					<input name='title' type="text" value="<?php echo $title; ?>" />
					<br>
					<input name="editThread" type="button" value="スレッド名変更"" onClick="validate_thrd(this.form, this)"/>
					<input name="deleteThread" type="button" value="スレッド削除" onClick="validate_thrd(this.form, this)"/>
					<!-- hidden -->
					<input name="action" type="hidden" value="" />
					<input name="path" type="hidden" value="edit" />
					<input name="id" type="hidden" value="<?php echo $id ?>" />
				</form>
				<hr>
				<!-- 書き込み内容編集 -->
				<form name="editContentsForm"  method="post" action="./class/MakeController.php" >
					番号：<br>
					<select name="noSlct" onchange="setCont(this)">
<?php
						// セレクトボックスの中身を作成
						for($i = 1; $i < count($cont) + 1; $i++) {
							echo "<option value='" . $i . "'>" . $i . "</option>";
						}
?>
					</select>
					<br>
					名前：<br>
					<input name="writer" type="text" value="<?php echo $cont[0]['writer']; ?>" /><br>
					内容：<br>
					<textarea name="writetext" col="40" rows="5"><?php echo $cont[0]['writetext']; ?></textarea><br>
					<input name="editContents" type="button" value="書き込み変更""  onClick="validate_cont(this.form, this)"/>
					<input name="deleteContents" type="button" value="書き込み削除""  onClick="validate_cont(this.form, this)" <?php echo $contsDelBtn; ?>/>
					<!-- hidden -->
					<input name="action" type="hidden" value="" />
					<input name="path" type="hidden" value="edit" />
					<input name="id" type="hidden" value="<?php echo $cont[0]['id']; ?>" />
					<input name="thread_list_id" type="hidden" value="<?php echo $id; ?>" />
					<input name="title" type="hidden" value="<?php echo $title; ?>" />
				</form>
			</div>
		</div>
		<br>
		<a href="./index.php">一覧へ戻る</a>
	</div>
</body>
</html>
