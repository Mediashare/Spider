<?php
require 'vendor/autoload.php';

$url = 'http://marquand.pro';
$options = [
    'id' => 'tes2t', // Id|Name report
    'webspider' => true, // Crawl all website
    'require' => [], // Path required
    'exception' => [], // Path exceptions
    // Prompt console options
    'html' => true, // Html output
    'json' => false,  // Json output
    // Directory
    'reports_dir' => __DIR__.'/../../reports/', // Default reports path
    'modules_dir' => __DIR__.'/src/Modules/', // Default modules path
    // Modules
    'modules' => ['Links'], // Select one or more modules to use with class name
    'enable_modules' => false, // Enable all modules
];

$spider = new \Mediashare\Spider($url, $options);
$result = $spider->run();
dump($result);

