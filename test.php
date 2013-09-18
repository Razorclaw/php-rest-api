<?php
include 'RestApi.php';

$api = new RestApi();

$api->on('post', function($data) {
	return 'post !';
});

$api->on('get', function($data) {
	return 'get !';
});

$api->handle();
?>
