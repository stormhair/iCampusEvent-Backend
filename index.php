<?php

require 'controller.php';

if(isset($_GET['action'])) {
	$action = $_GET['action'];
	switch ($action) {
		case 'get_all_events':
			# code...
			Controller::get_all_events();
			break;
		default:
			# code...
			echo_error(ACTION_NOT_EXIST);
	}
} else {
	echo_error(ACTION_ERROR);
}
