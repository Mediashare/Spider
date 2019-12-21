<?php
require 'vendor/autoload.php';
use Tracy\Debugger;
Debugger::enable();

$config = new \Mediashare\Entity\Config(); // Website Config
$config->setRequires(['/images/', '/tags/']); // Path requires

// Url
$url = new \Mediashare\Entity\Url('http://marquand.pro');

// Spider
$spider = new \Mediashare\Spider($url, $config);
$result = $spider->run();
// var_dump($result);