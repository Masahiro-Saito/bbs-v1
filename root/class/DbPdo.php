<?php

/**
 * DBクラス
 */
Class DbPdo extends PDO {
	// DB接続情報
	const dburl = "mysql:dbname=db01;host=localhost";
	const dbname = "user01";
	const dbpass = "user01";

	// インスタンス
	protected static $db;
	// データセット
	protected static $dsn;

	public function __construct() {
		parent::__construct(self::dburl, self::dbname, self::dbpass,
			array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`")
		);
	}

	/**
	 * DB接続
	 */
	public static function connect() {
		// DBに未接続であればDBに接続する
		if(! self::$db) {
			self::$db = new self();
			// DB関連の処理失敗時は例外を投げるように設定
			self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}

		return self::$db;
	}
}