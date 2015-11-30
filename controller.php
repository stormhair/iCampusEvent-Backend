<?php

require 'helper.php';

$loc2id = ['UPC' => 0, 'RRI' => 1, 'ZNI' => 2, 'THH' => 3, 'ANN' => 4, 'AHF' => 5, 'DML' => 6, 'ASC' => 7, 'PAM-OFFCAM' => 8, 'SGM' => 9, 'RGL' => 10, 'ZHS' => 11, 'RTH' => 12, 'LRC' => 13];

class Controller {
	
	static private $mysqli = null;
	
	static private function get_mysqi() {
		if(static::$mysqli == null) {
			static::$mysqli = new mysqli('localhost', 'root', '', 'events');
			//DB connection error
			if (static::$mysqli->connect_errno) {
				echo_error(DB_CONNECTION_ERROR);
			}
		}
		return static::$mysqli;
	}

	static private function add_string_time($res) {
		date_default_timezone_get('America/Los_Angeles');
		foreach($res as &$rec) {
			if(isset($rec['start_timestamp'])) {
				$strTime = date('m/d H:i', $rec['start_timestamp']);
				$rec['start_timestr'] = $strTime;
			}
			if(isset($rec['end_timestamp'])) {
				$strTime='';
				if($rec['end_timestamp']!=-1) {
					$strTime = date('m/d H:i', $rec['end_timestamp']);
				}
				$rec['end_timestr'] = $strTime;
			}
		}
		return $res;
	}

	static private function randomFloat($min = -0.000005, $max = 0.000005) {
		return $min + mt_rand() / mt_getrandmax() * ($max - $min);
	}

	static private function randomize_location($res) {
		$location = ['STUB'=>1];
		foreach($res as &$rec) {
			if(array_key_exists($rec['abbr'], $location)) {
				$rec['lat'] += static::randomFloat(-0.000005, 0.000005);
				$rec['lng'] += static::randomFloat(-0.000005, 0.000005);
			}
			else {
				$location[$rec['abbr']] = 1;
			}
		}
		return $res;
	}

	/**
	 * @return all events
	 */
	static public function get_all_events() {
		$mysqli = static::get_mysqi();
		$res = $mysqli->query('SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng FROM events AS e, locations AS l where e.location = l.location_id');
		$events = mysqli_fetch_all($res, MYSQLI_ASSOC);
		$events = static::add_string_time($events);
		$events = static::randomize_location($events);
		echo_response(['events'=>$events]);
	}

	/**
	 * @return all events that are not terminated now
	 */
	static public function get_future_events() {
		$mysqli = static::get_mysqi();
		$curTime = time();
		$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng FROM events AS e, locations AS l where e.location = l.location_id AND $curTime < e.start_timestamp");
		$events = mysqli_fetch_all($res, MYSQLI_ASSOC);
		$events = static::add_string_time($events);
		echo_response(['events'=>$events]);
	}
	
	/**
	 * @param start unix time stamp
	 * @param end unix time stamp
	 * @return all events held after given time
	 */
	static public function get_events_after($start_time) {
		$mysqli = static::get_mysqi();
		$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng FROM events AS e, locations AS l where e.location = l.location_id AND $start_time <= e.start_timestamp");
		$events = mysqli_fetch_all($res, MYSQLI_ASSOC);
		$events = static::add_string_time($events);
		echo_response(['events'=>$events]);
	}

	/**
	 * @param keywords
	 * @return all events related to the given keyword string
	 */
	static public function get_events_related_to($keyword) {
		$mysqli = static::get_mysqi();
		$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng FROM events AS e, locations AS l where e.location = l.location_id AND (l.location_name LIKE '%$keyword%' OR e.event_title LIKE '%$keyword%' OR e.event_subtitle LIKE '%$keyword%')");
		$events = mysqli_fetch_all($res, MYSQLI_ASSOC);
		$events = static::add_string_time($events);
		echo_response(['events'=>$events]);
	}

	static public function get_events_at($location) {
		global $loc2id;
		if(isset($loc2id[$location])) {
			$location = $loc2id[$location];
		}
		else {
			$location = 0;
		}
		$mysqli = static::get_mysqi();
		$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng FROM events AS e, locations AS l where e.location = l.location_id AND e.location = $location");
		$events = mysqli_fetch_all($res, MYSQLI_ASSOC);
		$events = static::add_string_time($events);
		echo_response(['events'=>$events]);
	}

	/**
	 * @param keyword
	 * @param locaiton abbreviation
	 * @return all events held within one day
	 */
	static public function get_events_keyword_location_within_one_day($keyword, $location) {
		global $loc2id;
		if(isset($loc2id[$location])) {
			$location = $loc2id[$location];
		}
		else {
			$location = 0;
		}
		$mysqli = static::get_mysqi();
		$curTime = intval(time());
		$endtime = $curTime+3600*24;
		$res=[];
		if($keyword == '') {
			if($location!=0) {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND e.start_timestamp <= $endtime AND e.location = $location");
			}
			else {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND e.start_timestamp <= $endtime");
			}
		}
		else {
			if($location!=0) {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND e.start_timestamp <= $endtime AND e.location = $location AND (l.location_name LIKE '%$keyword%' OR e.event_title LIKE '%$keyword%' OR e.event_subtitle LIKE '%$keyword%')");
			}
			else {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND e.start_timestamp <= $endtime AND (l.location_name LIKE '%$keyword%' OR e.event_title LIKE '%$keyword%' OR e.event_subtitle LIKE '%$keyword%')");
			}
		}
		$events = mysqli_fetch_all($res, MYSQLI_ASSOC);
		$events = static::add_string_time($events);
		echo_response(['events'=>$events]);
	}

	/**
	 * @param keyword
	 * @param locaiton abbreviation
	 * @return all events held within one week
	 */
	static public function get_events_keyword_location_within_one_week($keyword, $location) {
		global $loc2id;
		if(isset($loc2id[$location])) {
			$location = $loc2id[$location];
		}
		else {
			$location = 0;
		}
		$mysqli = static::get_mysqi();
		$curTime = intval(time());
		$endtime = $curTime+3600*24*7;
		if($keyword == '') {
			if($location!=0) {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND e.start_timestamp <= $endtime AND e.location = $location");
			}
			else {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND e.start_timestamp <= $endtime");
			}
		}
		else {
			if($location!=0) {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND e.start_timestamp <= $endtime AND e.location = $location AND (l.location_name LIKE '%$keyword%' OR e.event_title LIKE '%$keyword%' OR e.event_subtitle LIKE '%$keyword%')");
			}
			else {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND e.start_timestamp <= $endtime AND (l.location_name LIKE '%$keyword%' OR e.event_title LIKE '%$keyword%' OR e.event_subtitle LIKE '%$keyword%')");
			}
		}
		$events = mysqli_fetch_all($res, MYSQLI_ASSOC);
		$events = static::add_string_time($events);
		echo_response(['events'=>$events]);
	}

	/**
	 * @param keyword
	 * @param locaiton abbreviation
	 * @return all events held within one month
	 */
	static public function get_events_keyword_location_within_one_month($keyword, $location) {
		global $loc2id;
		if(isset($loc2id[$location])) {
			$location = $loc2id[$location];
		}
		else {
			$location = 0;
		}
		$mysqli = static::get_mysqi();
		$curTime = intval(time());
		$endtime = $curTime+3600*24*30;
		if($keyword == '') {
			if($location!=0) {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND e.start_timestamp <= $endtime AND e.location = $location");
			}
			else {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND e.start_timestamp <= $endtime");
			}
		}
		else {
			if($location!=0) {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND e.start_timestamp <= $endtime AND e.location = $location AND (l.location_name LIKE '%$keyword%' OR e.event_title LIKE '%$keyword%' OR e.event_subtitle LIKE '%$keyword%')");
			}
			else {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND e.start_timestamp <= $endtime AND (l.location_name LIKE '%$keyword%' OR e.event_title LIKE '%$keyword%' OR e.event_subtitle LIKE '%$keyword%')");
			}
		}
		$events = mysqli_fetch_all($res, MYSQLI_ASSOC);
		$events = static::add_string_time($events);
		echo_response(['events'=>$events]);
	}
	
	/**
	 * @param keyword
	 * @param locaiton abbreviation
	 * @return all statisfied events held
	 */
	static function get_events_keyword_location($keyword, $location) {
		global $loc2id;
		if(isset($loc2id[$location])) {
			$location = $loc2id[$location];
		}
		else {
			$location = 0;
		}
		$mysqli = static::get_mysqi();
		$curTime = intval(time());
		$endtime = $curTime+3600*24*30;
		if($keyword == '') {
			if($location!=0) {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND e.location = $location");
			}
			else {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id");
			}
		}
		else {
			if($location!=0) {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND e.location = $location AND (l.location_name LIKE '%$keyword%' OR e.event_title LIKE '%$keyword%' OR e.event_subtitle LIKE '%$keyword%')");
			}
			else {
				$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.event_desc, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.abbr, l.lat, l.lng
								FROM events AS e, locations AS l
								where e.location = l.location_id AND (l.location_name LIKE '%$keyword%' OR e.event_title LIKE '%$keyword%' OR e.event_subtitle LIKE '%$keyword%')");
			}
		}
		//$events = mysqli_fetch_all($res, MYSQLI_ASSOC);
		$events = [];
		while($row = $res->fetch_assoc()) {
			$events[] = $row;
		}
		$events = static::add_string_time($events);
		$events = static::randomize_location($events);
		echo_response(['events'=>$events]);
	}

	static public function test() {
		$mysqli = static::get_mysqi();
		$res = $mysqli->query('SELECT event_id, page_url FROM events');
		$events = mysqli_fetch_all($res, MYSQLI_ASSOC);
		echo_response(['events'=>$events]);
	}
}
