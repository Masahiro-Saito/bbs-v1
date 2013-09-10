<?php

class ThreadController {


/***********************************************************
 * DB操作
 ***********************************************************/
	/**
	 * スレッドの一覧を取得する
	 */
	public function getThreadList() {
		// DB接続
		$dbPdo = DbPdo::connect();
		// SQL
		$sql = "select id, title from thread_lists;";
		// 実行
		$ret = $dbPdo->query($sql);

		return $ret->fetchAll();
	}

	/**
	 * スレッドの書き込みを取得する
	 * id=ThreadListsのid
	 * cnt=書き込みの取得件数
	 */
	public function getThreadContents($id, $num) {
		// DB接続
		$dbPdo = DbPdo::connect();
		// SQL
		$sql = "select " .
					"id, " .
					"thread_list_id, " .
					"writer, " .
					"writetime, " .
					"writetext " .
				"from " .
					"thread_contents " .
				"where " .
					"thread_list_id = $id ";

		// 取得件数を制限する
		if ($num > 0) {
			$sql = $sql . "limit $num";
		}

		// 実行
		$ret = $dbPdo->query($sql);

		return $ret->fetchAll();
	}

/***********************************************************
 * 画面操作（Viewを作るのは結合度が・・・）
***********************************************************/
	/**
	 * スレッドの一覧を作成する
	 */
	public function makeThreadList() {
		// 画面表示を作成
		foreach(self::getThreadList() as $list) {
			echo "<a href=./thread.php?id=$list[id]&title=$list[title] />" . $list['title'] . "</a>";
			echo "<br>";
		}
	}

}
