<?php
require 'vendor/autoload.php';
use Tracy\Debugger;
Debugger::enable();

$config = new \Mediashare\Entity\Config(); // Website Config
$config->setVerbose(true); // Prompt verbose output
// Url
$url = new \Mediashare\Entity\Url('http://marquand.pro');

// Spider
$spider = new \Mediashare\Spider($url, $config);
$result = $spider->run();