<?php
require 'vendor/autoload.php';
// use Tracy\Debugger;
// Debugger::enable();

$config = new \Mediashare\Spider\Entity\Config(); // Website Config
// $config->setId("Audit_marquand.pro"); // Id|Name report (uniqid() by default)
$config->setWebspider(true); // Crawl all website

// Directories
$config->setReportsDir(__DIR__.'/reports/'); // Default reports path
$config->setModulesDir(__DIR__.'/modules/'); // Default modules path

// Prompt Console / Dump
$config->setVerbose(true); // Prompt verbose output
// $config->setJson(true); // Prompt json output

// Modules Activation
$config->enableDefaultModule(false); // Enable default SEO kernel modules

// Url
$url = new \Mediashare\Spider\Entity\Url('https://www.exacompare.fr/');

// Spider
$spider = new \Mediashare\Spider\Spider($url, $config);
$result = $spider->run();
// dump($result);