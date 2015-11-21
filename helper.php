<?php
const ACTION_ERROR = 1;
const DB_ERROR = 2;
const ACTION_NOT_EXIST = 3;

function echo_error($error_code, $error_message='') {
	static $error_mapping = [
		1 => 'Action not provided!',
		2 => 'DB cannot be connected!',
		3 => 'Action not exist'
	];
	if(array_key_exists($error_code, $error_mapping)) {
		$error_message = $error_mapping[$error_code];
	}
	echo json_encode([
			'error_code' => $error_code,
			'error_meesage' => $error_message
		]);
	exit($error_code);
}

function echo_response($result) {
	$result['error_code'] = 0;
	echo json_encode($result);
	exit(0);
}