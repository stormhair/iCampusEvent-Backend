<?php
const OK = 0;
const ACTION_ERROR = 1;
const DB_CONNECTION_ERROR = 2;
const ACTION_NOT_EXIST = 3;
const PARAMETER_NOT_SET = 4;
const PARAMETER_LOGIC_ERROR = 5;

function echo_error($error_code, $error_message='') {
	static $error_mapping = [
		0 => 'No error',
		1 => 'Action not provided!',
		2 => 'DB cannot be connected!',
		3 => 'Action not exist',
		4 => 'Parameters are not set',
		5 => 'Parameters are not in logic'
	];
	if(array_key_exists($error_code, $error_mapping)) {
		$error_message = $error_mapping[$error_code];
	}
	echo json_encode([
			'error_code' => $error_code,
			'error_message' => $error_message
		]);
	exit($error_code);
}

function echo_response($result) {
	$result['error_code'] = 0;
	echo json_encode($result);
	exit(0);
}