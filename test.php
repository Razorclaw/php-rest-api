<?php
include 'RestApi.php';

$api = new RestApi('json');

$api->bind('post', function($data)
{
    return array('method' => 'post');
})->bind('get', function($data, $e)
{
    $e->raise(404, "Nooot Founds");
})->bind('put', function($data)
{
    return $data;
})->handle();
?>
