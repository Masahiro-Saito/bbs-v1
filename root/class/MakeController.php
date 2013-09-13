<?php
require_once("./DbPdo.php");
require_once("./ThreadController.php");
require_once 'HTTP.php';

// 共通パラメータを取得
$path = $_POST['path'];
$action = $_POST['action'];

// インスタンス
$makeCtrl = new MakeController();

// 指定アクションにより処理を行う
switch($action) {
// スレッド(新規作成)
case "addThread":
	// パラメータ格納
	$param = array();
	$param['title'] = $_POST['title'];
	$param['writer'] = $_POST['writer'];
	$param['writetext'] = $_POST['writetext'];

	// 処理開始
	$makeCtrl->makeThread($param);
	// ページ遷移
	HTTP::redirect("../$path.php");
	break;
// 書き込み(追加作成)
case "addContents":
	// パラメータ格納
	$param = array();
	$param['id'] = $_POST['id'];
	$param['writer'] = $_POST['writer'];
	$param['writetext'] = $_POST['writetext'];

	// 処理開始
	$makeCtrl->makeContents($param);
	// ページ遷移
	HTTP::redirect("../$path.php?id=$_POST[id]&title=$_POST[title]");
	break;
// スレッド(編集・削除)
case "editThread":
	// パラメータ格納
	$param = array();
	$param['id'] = $_POST['id'];
	$param['title'] = $_POST['title'];

	// 処理開始
	$makeCtrl->editThread($param);
	HTTP::redirect("../$path.php?id=$_POST[id]&title=$_POST[title]");
	break;
case "deleteThread":
	// パラメータ格納
	$param = array();
	$param['id'] = $_POST['id'];
	$param['title'] = $_POST['title'];

 	// 処理開始
 	$makeCtrl->delThread($param);
 	// スレッド削除時は強制的にindexへ戻る
 	HTTP::redirect("../index.php");
	break;
case "editContents":
	// パラメータ格納
	$param = array();
	$param['id'] = $_POST['id'];
	$param['writer'] = $_POST['writer'];
	$param['writetext'] = $_POST['writetext'];
	$param['thread_list_id'] = $_POST['thread_list_id'];

	// 処理開始
	$makeCtrl->editContents($param);
	HTTP::redirect("../$path.php?id=$_POST[thread_list_id]&title=$_POST[title]");
	break;
case "deleteContents":
	// パラメータ格納
	$param = array();
	$param['id'] = $_POST['id'];
	$param['thread_list_id'] = $_POST['thread_list_id'];

	// 処理開始
	$makeCtrl->delContents($param);
	HTTP::redirect("../$path.php?id=$_POST[thread_list_id]&title=$_POST[title]");
	break;
}

/**
 * 追加・変更・削除の実処理を実行するクラス
 */
class MakeController {
	/**
	 *新規スレッド作成処理を行う
	 * @param $param フォームの入力値を格納した連想配列
	 */
	public function makeThread($param) {
		// 例外発生時はError画面に遷移させる
		// 戻る場合はjavascriptのhistorybackで2ページ分戻らせればOK？

		// DB接続
		$dbPdo = DbPdo::connect();
		// トランザクション(autoCommit対策)
		$dbPdo->beginTransaction();
		// 新規スレッド作成
		self::insertThread($dbPdo, $param['title']);
		// 新規スレッドのIDを取得
		$id = self::getNewThreadId($dbPdo);
		// 新規書き込み作成
		self::insertContents($dbPdo, $id, $param['writer'], $param['writetext']);
		// コミット
		$dbPdo->commit();
	}

	/**
	 * 追加書き込み作成処理を行う
	 * @param $param フォームの入力値を格納した連想配列
	 */
	public function makeContents($param) {
		// DB接続
		$dbPdo = DbPdo::connect();
		// トランザクション(autoCommit対策)
		$dbPdo->beginTransaction();
		// 新規書き込み作成
		self::insertContents($dbPdo, $param['id'], $param['writer'], $param['writetext']);
		// コミット
		$dbPdo->commit();
	}

	/**
	 * スレッドの更新処理を行う
	 * @param $param フォームの入力値を格納した連想配列
	 */
	public function editThread($param) {
		// DB接続
		$dbPdo = DbPdo::connect();
		// トランザクション(autoCommit対策)
		$dbPdo->beginTransaction();
		// タイトル変更
		self::updateThread($dbPdo, $param['id'], $param['title']);
		// コミット
		$dbPdo->commit();
	}

	/**
	 * スレッドの削除処理を行う
	 * @param $param フォームの入力値を格納した連想配列
	 */
	public function delThread($param) {
		// DB接続
		$dbPdo = DbPdo::connect();
		// トランザクション(autoCommit対策)
		$dbPdo->beginTransaction();
		// 書き込み全件削除
		self::deleteThreadContentsAll($dbPdo, $param['id']);
		// スレッド削除
		self::deleteThread($dbPdo, $param['id']);
		// コミット
		$dbPdo->commit();
	}

	/**
	 * 書き込みの更新処理を行う
	 * @param $param フォームの入力値を格納した連想配列
	 */
	public function editContents($param) {
		// DB接続
		$dbPdo = DbPdo::connect();
		// トランザクション(autoCommit対策)
		$dbPdo->beginTransaction();
		// 書き込み変更
		self::updateContents($dbPdo, $param['id'], $param['thread_list_id'], $param['writer'], $param['writetext']);
		// コミット
		$dbPdo->commit();
	}

	/**
	 * 書き込みの削除処理を行う
	 * @param $param フォームの入力値を格納した連想配列
	 */
	public function delContents($param) {
		// DB接続
		$dbPdo = DbPdo::connect();
		// トランザクション(autoCommit対策)
		$dbPdo->beginTransaction();
		// 書き込み変更
		self::deleteThreadContents($dbPdo, $param['id'], $param['thread_list_id']);
		// コミット
		$dbPdo->commit();
	}
/************************************************************
 * 内部処理
 ************************************************************/
	/**
	 * 新規スレッドをDBにInsertする
	 * @param $dbPdo PDO
	 * @param $title 新規スレッドのタイトル
	 */
	private function insertThread($dbPdo, $title) {
/*
		// SQL
		// 文字列を使用する場合は''で囲まないといけない
		$sql = "insert into thread_lists(title) values ('$title')";
		// 実行
		$dbPdo->exec($sql);
		$dbPdo->commit();
*/
		// SQL
		// 文字列、数値はSQL側で判断される
		$sql = "insert into thread_lists(title) values (?)";
		$stmt = $dbPdo->prepare($sql);
		// パラメータ設定(場所, パラメータ内容)
		$stmt->bindParam(1, $title);
		// 実行
		$stmt->execute();
	}

	/**
	 * 新規スレッドのIDを取得する
	 * @param $dbPdo PDO
	 */
	private function getNewThreadId($dbPdo) {
		// SQL
		$sql = "select max(id) as id from thread_lists";
		// 実行
		$ret = $dbPdo->query($sql);
		// 結果取得
		$arr = $ret->fetch();

		return $arr['id'];
	}

	/**
	 * 新規書き込みをDBにInsertする
	 * @param $dbPdo PDO
	 * @param $id 新規スレッドのID
	 * @param $writer 名前
	 * @param $writetext 内容
	 */
	private function insertContents($dbPdo, $id, $writer, $writetext) {
		// SQL
		// 文字列、数値はSQL側で判断される
		$sql = "insert into thread_contents " .
					"(thread_list_id, writer, writetext) " .
					"values " .
					"(?, ?, ?)";
		$stmt = $dbPdo->prepare($sql);
		// パラメータ設定(場所, パラメータ内容)
		$stmt->bindParam(1, $id);
		$stmt->bindParam(2, $writer);
		$stmt->bindParam(3, $writetext);
		// 実行
		$stmt->execute();
	}

	/**
	 * スレッドのタイトルをupdateする
	 * @param $dbPdo PDO
	 * @param $id 更新するスレッドのID
	 * @param $title タイトル
	 */
	private function updateThread($dbPdo, $id, $title) {
		// SQL
		// 文字列、数値はSQL側で判断される
		$sql = "update thread_lists set title = ? where id = ?";
		$stmt = $dbPdo->prepare($sql);
		// パラメータ設定(場所, パラメータ内容)
		$stmt->bindParam(1, $title);
		$stmt->bindParam(2, $id);
		// 実行
		$stmt->execute();
	}

	/**
	 * スレッドをdeleteする
	 * @param $dbPdo PDO
	 * @param $id 削除するスレッドのID
	 * @param $title タイトル
	 */
	private function deleteThread($dbPdo, $id) {
		// SQL
		// 文字列、数値はSQL側で判断される
		$sql = "delete from thread_lists where id = ?";
		$stmt = $dbPdo->prepare($sql);
		// パラメータ設定(場所, パラメータ内容)
		$stmt->bindParam(1, $id);
		// 実行
		$stmt->execute();
	}

	/**
	 * スレッドの書き込みを一括deleteする
	 * @param $dbPdo PDO
	 * @param $thread_list_id 削除するスレッドのID
	 * @param $title タイトル
	 */
	private function deleteThreadContentsAll($dbPdo, $thread_list_id) {
		// SQL
		// 文字列、数値はSQL側で判断される
		$sql = "delete from thread_contents where thread_list_id = ?";
		$stmt = $dbPdo->prepare($sql);
		// パラメータ設定(場所, パラメータ内容)
		$stmt->bindParam(1, $thread_list_id);
		// 実行
		$stmt->execute();
	}

	/**
	 * 書き込み内容をupdateする
	 * @param $dbPdo PDO
	 * @param $id 編集する書き込みのID
	 * @param $thread_list_id　編集する書き込みが存在するスレッドのID
	 * @param $writer 名前
	 * @param $writetext 書き込み内容
	 */
	private function updateContents($dbPdo, $id, $thread_list_id, $writer, $writetext) {
		// SQL
		// 文字列、数値はSQL側で判断される
		$sql = "update thread_contents ".
				"set " .
					"writer = ?, " .
					"writetext = ? " .
				"where " .
					"id = ? " .
					"and " .
					"thread_list_id = ? ";
		$stmt = $dbPdo->prepare($sql);
		// パラメータ設定(場所, パラメータ内容)
		$stmt->bindParam(1, $writer);
		$stmt->bindParam(2, $writetext);
		$stmt->bindParam(3, $id);
		$stmt->bindParam(4, $thread_list_id);
		// 実行
		$stmt->execute();
	}

	/**
	 * スレッドの書き込みを指定したものだけdeleteする
	 * @param $dbPdo PDO
	 * @param $id 削除する書き込み内容のID
	 * @param $thread_list_id 削除するスレッドのID
	 * @param $title タイトル
	 */
	private function deleteThreadContents($dbPdo, $id, $thread_list_id) {
		// SQL
		// 文字列、数値はSQL側で判断される
		$sql = "delete from thread_contents where id = ? and thread_list_id = ?";
		$stmt = $dbPdo->prepare($sql);
		// パラメータ設定(場所, パラメータ内容)
		$stmt->bindParam(1, $id);
		$stmt->bindParam(2, $thread_list_id);
		// 実行
		$stmt->execute();
	}
}
