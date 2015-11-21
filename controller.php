<?php

require 'helper.php';

class Controller {
	static private $mysqli = null;

	static public function get_all_events() {
		$mysqli = static::get_mysqi();
		$res = $mysqli->query("SELECT * FROM events");
		$events = $res->fetch_all();
		echo_response(['events'=>$events]);
	}

	static private function get_mysqi() {
		if(static::$mysqli == null) {
			static::$mysqli = new mysqli("localhost", "root", "", "events");
			//DB connection error
			if (static::$mysqli->connect_errno) {
				echo_error(DB_ERROR);
			}
		}
		return static::$mysqli;
	}

}
