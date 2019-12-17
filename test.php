<?php
require 'vendor/autoload.php';
$url = 'http://marquand.pro';
$input = [
    'id' => 'test',
    'webspider' => true,
    'json' => true
];

$spider = new \Spider\Spider($url, $input);
$spider->run();






