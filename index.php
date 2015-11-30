<?php

require 'controller.php';

if(isset($_GET['action'])) {
	$action = $_GET['action'];
	switch ($action) {
		case 'get_all_events':
			# code...
			Controller::get_all_events();
			break;
		case 'get_future_events':
			Controller::get_future_events();
			break;
		case 'get_events_after':
			if(isset($_GET['start_time'])){
				$beg = $_GET['start_time'];
				Controller::get_events_after($beg);
			}
			else {
				echo_error(PARAMETER_NOT_SET);
			}
			break;
		case 'get_events_related_to':
			if(isset($_GET['keyword'])) {
				$keyword = $_GET['keyword'];
				Controller::get_events_related_to($keyword);
			}
			else {
				echo_error(PARAMETER_NOT_SET);
			}
			break;
		case 'get_events_keyword_location_within_one_day':
			if(isset($_GET['keyword']) and isset($_GET['location'])) {
				$keyword = $_GET['keyword'];
				$location = $_GET['location'];
				Controller::get_events_keyword_location_within_one_day($keyword, $location);
			}
			else {
				echo_error(PARAMETER_NOT_SET);
			}
			break;
		case 'get_events_keyword_location_within_one_week':
			if(isset($_GET['keyword']) and isset($_GET['location'])) {
				$keyword = $_GET['keyword'];
				$location = $_GET['location'];
				Controller::get_events_keyword_location_within_one_week($keyword, $location);
			}
			else {
				echo_error(PARAMETER_NOT_SET);
			}
			break;
		case 'get_events_keyword_location_within_one_month':
			if(isset($_GET['keyword']) and isset($_GET['location'])) {
				$keyword = $_GET['keyword'];
				$location = $_GET['location'];
				Controller::get_events_keyword_location_within_one_month($keyword, $location);
			}
			else {
				echo_error(PARAMETER_NOT_SET);
			}
			break;
		case 'get_events_at':
			if(isset($_GET['location'])) {
				$location = $_GET['location'];
				Controller::get_events_at($location);
			}
			else {
				echo_error(PARAMETER_NOT_SET);
			}
			break;
		case 'get_events_keyword_location':
			if(isset($_GET['keyword']) and isset($_GET['location'])) {
				$keyword = $_GET['keyword'];
				$location = $_GET['location'];
				Controller::get_events_keyword_location($keyword, $location);
			}
			else {
				echo_error(PARAMETER_NOT_SET);
			}
			break;
		case 'test':
			Controller::test();
			break;
		default:
			# code...
			echo_error(ACTION_NOT_EXIST);
	}
} else {
	echo_error(ACTION_ERROR);
}
