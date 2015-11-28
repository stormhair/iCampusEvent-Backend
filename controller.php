<?php

require 'helper.php';

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

	/**
	 * @return all events
	 */
	static public function get_all_events() {
		$mysqli = static::get_mysqi();
		$res = $mysqli->query('SELECT e.event_title, e.event_subtitle, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.lat, l.lng FROM events AS e, locations AS l where e.location = l.location_id');
		$events = mysqli_fetch_all($res, MYSQLI_ASSOC);
		echo_response(['events'=>$events]);
	}

	/**
	 * @return all events that are not terminated now
	 */
	static public function get_future_events() {
		$mysqli = static::get_mysqi();
		$curTime = time();
		$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.lat, l.lng FROM events AS e, locations AS l where e.location = l.location_id AND $curTime < e.start_timestamp");
		$events = mysqli_fetch_all($res, MYSQLI_ASSOC);
		echo_response(['events'=>$events]);
	}
	
	/**
	 * @param start unix time stamp
	 * @param end unix time stamp
	 * @return all events held after given time
	 */
	static public function get_events_after($start_time) {
		$mysqli = static::get_mysqi();
		$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.lat, l.lng FROM events AS e, locations AS l where e.location = l.location_id AND $start_time <= e.start_timestamp");
		$events = mysqli_fetch_all($res, MYSQLI_ASSOC);
		echo_response(['events'=>$events]);
	}

	/**
	 * @param keywords
	 * @return all events related to the given keyword string
	 */
	static public function get_events_related_to($keyword) {
		$mysqli = static::get_mysqi();
		$res = $mysqli->query("SELECT e.event_title, e.event_subtitle, e.start_timestamp, e.end_timestamp, e.page_url, e.image_url, l.location_name, l.lat, l.lng FROM events AS e, locations AS l where e.location = l.location_id AND (l.location_name LIKE '%$keyword%' OR e.event_title LIKE '%$keyword%' OR e.event_subtitle LIKE '%$keyword%')");
		$events = mysqli_fetch_all($res, MYSQLI_ASSOC);
		echo_response(['events'=>$events]);
	}
