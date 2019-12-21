<?php
require 'vendor/autoload.php';
use Tracy\Debugger;
Debugger::enable();

// Config
$config = new \Mediashare\Entity\Config();
$config->setJson(true); // Prompt json response
// Url
$url = new \Mediashare\Entity\Url('http://marquand.pro');
// Spider
$spider = new \Mediashare\Spider($url, $config);
$result = $spider->run();