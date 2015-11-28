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
		default:
			# code...
			echo_error(ACTION_NOT_EXIST);
	}
} else {
	echo_error(ACTION_ERROR);
}
