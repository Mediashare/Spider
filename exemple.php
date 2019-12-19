<?php
require 'vendor/autoload.php';


$config = new \Mediashare\Entity\Config(); // Website Config
// $config->setId("Audit"); // Id|Name report (uniqid() by default)
$config->setWebspider(true); // Crawl all website
$config->setRequires([]); // Path requires
$config->setExceptions([]); // Path exceptions
// Directories
$config->setReportsDir(__DIR__.'/reports/'); // Default reports path
$config->setModulesDir(__DIR__.'/src/Modules/'); // Default modules path
// Prompt Console / Dump
$config->setVerbose(true); // Prompt verbose output
$config->setJson(false); // Prompt json output
// Modules Activation
$config->enableAllModule(true); // Enable all modules
// $config->addModules(['Links', 'Search']);// Select one or more modules to use with class name
// $config->addVariables(['Search' => ['Thibault Marquand']]); // Inject this variables in modules 

// Url
$url = new \Mediashare\Entity\Url('http://marquand.pro');

// Spider
$spider = new \Mediashare\Spider($url, $config);
$result = $spider->run();
// dump($result);

