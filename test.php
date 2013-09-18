<?php
include 'RestApi.php';

$api = new RestApi('json');

$api->on('post', function($data) {
	return array('method' => 'post');
});

$api->on('get', function($data) {
	return array('method' => 'get');
});

$api->on('put', function($data) {
	return $data;
});

$api->handle();
?>
