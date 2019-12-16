<?php
require 'Spider.php';
// require 'vendor/autoload.php';
$url = 'http://marquand.pro';
$input = [
    'id' => 'test',
    'webspider' => true,
    'json' => true
];

$spider = new \Mediashare\SpiderInterface($url, $input);
$spider->run();






