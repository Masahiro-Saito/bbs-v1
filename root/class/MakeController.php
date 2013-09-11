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
// 新規スレッド作成
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
// 追加書き込み作成
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
		echo $sql;
		// 実行
		$stmt->execute();
	}
}
