<?php
require 'vendor/autoload.php';

$url = 'http://marquand.pro';
$input = [
    'id' => 'test',
    'webspider' => true,
    'json' => true, 
    'reports_dir' => __DIR__.'/reports/',
    'modules_dir' => __DIR__.'/modules/',
];

$spider = new \Spider\Spider($url, $input);
$spider->run();