<?php
require 'vendor/autoload.php';

$url = 'http://marquand.pro';
$options = [
    'id' => 'tes2t', // Id|Name report
    'webspider' => true, // Crawl all website
    'require' => [], // Path required
    'exception' => [], // Path exceptions
    'prompt' => [ // Prompt console options
        'html' => true, // Html output
        'json' => false,  // Json output
    ],
    'modules_dir' => __DIR__.'/src/Modules/', // Default modules path
    'reports_dir' => __DIR__.'/../../reports/', // Default reports path
    'all_modules' => false, // Enable all modules
    'disable_modules' => false, // Disable all modules
    'modules' => ['Links'] // Select one or more modules to use with class name
];

$spider = new \Mediashare\Spider($url, $options);
$result = $spider->run();
// dump($result);

