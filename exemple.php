<?php
require 'vendor/autoload.php';

$url = 'http://marquand.pro';
$input = [
    'id' => 'test',
    'webspider' => true,
    'prompt' => [
        'html' => true,
        'json' => false,  
    ],
    'reports_dir' => __DIR__.'/var/reports/',
    'modules_dir' => __DIR__.'/src/Modules/',
    'all_modules' => false,
    'modules' => ['Links']
];

$spider = new \Spider\Spider($url, $input);
$result = $spider->run();
// dump($result);