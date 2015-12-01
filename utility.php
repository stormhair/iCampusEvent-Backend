<?php

require 'helper.php';

$month_mapping = ['January'=>1, 'Feburary'=>2, 'March'=>3, 'April'=>4, 'May'=>5, 'June'=>6, 'July'=>7, 'August'=>8, 'September'=>9, 'October'=>10, 'November'=>11, 'December'=>12];

function get_start_end_timestamp($date, $time) {
	//...
}

function get_location() {
	//...
}

function save_to_database($res) {
	$mysqli = new mysqli('localhost', 'root', '', 'events');
	//DB connection error
	if (static::$mysqli->connect_errno) {
		echo_error(DB_CONNECTION_ERROR);
		return;
	}
	foreach($arr as $item) {
		$start_end_time = get_start_end_timestamp($item['date'], $item['time']);
		$loc = get_location($item['location']);
		$res = $mysqli->query("INSERT INTO events VALUES(${item['event_id']}, ${item['event_title']}, ${item['event_subtitle']}, ${start_end_time[0]}), ${start_end_time[1]}, $loc, ${item['page_url']}, ${item['img_url']}");
		//
	}
}
