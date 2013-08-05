<?php
error_reporting(E_ALL);
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

require 'Predis/Autoloader.php';
Predis\Autoloader::register();

$redis = new Predis\Client();
$obj = array(
	'created' => time(),
    'name'      => $_POST['name'],
    'message'   => nl2br(strip_tags($_POST['message'])),
    'channel'   => $_POST['channel'],
);

$json = json_encode($obj);
$redis->publish($_POST['channel'], $json);

echo $json;