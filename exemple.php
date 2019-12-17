<?php
require 'vendor/autoload.php';

$url = 'http://marquand.pro';
$input = [
    'id' => 'test',
    'webspider' => true,
    'json' => true, 
    'reports_dir' => __DIR__.'/var/reports/',
    'modules_dir' => __DIR__.'/src/Modules/',
    'all_modules' => true,
];

$spider = new \Spider\Spider($url, $input);
$spider->run();