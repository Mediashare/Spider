<?php
require 'vendor/autoload.php';
// use Tracy\Debugger;
// Debugger::enable();

$config = new \Mediashare\Spider\Entity\Config(); // Website Config
// $config->setId("Audit_marquand.pro"); // Id|Name report (uniqid() by default)
// $config->setWebspider(true); // Crawl all website
// $config->setRequires(['/projet']); // Path requires
// // $config->setExceptions(['/contact']); // Path exceptions
// // Directories
// $config->setReportsDir(__DIR__.'/reports/'); // Default reports path
// $config->setModulesDir(__DIR__.'/modules/'); // Default modules path
// // Prompt Console / Dump
$config->setVerbose(true); // Prompt verbose output
// $config->setJson(true); // Prompt json output
// // Modules Activation
// $config->enableAllModule(false); // Enable all modules
// $config->addModules(['Links', 'Search', 'Metadata']);// Select one or more modules to use with class name
// $config->addVariables(['Search' => ['ces deux exchanges']]); // Inject this variables in modules 

// Url
$url = new \Mediashare\Spider\Entity\Url('https://slote.me');

// Spider
$spider = new \Mediashare\Spider\Spider($url, $config);
$result = $spider->run();
// dump($result);