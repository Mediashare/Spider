<?php
require 'vendor/autoload.php';
use Tracy\Debugger;
Debugger::enable();

$config = new \Mediashare\Entity\Config();
$config->addModules(['Links', 'Metadata']); // Select one or more modules to use with class name

// Url
$url = new \Mediashare\Entity\Url('http://marquand.pro');

// Spider
$spider = new \Mediashare\Spider($url, $config);
$result = $spider->run();
// var_dump($result);