<?php
require 'vendor/autoload.php';
use Tracy\Debugger;
Debugger::enable();

// Website Config
$config = new Config();
$config->setWebspider(true); // Crawl all website
$config->setReportsDir(__DIR__.'/reports/'); // Default reports path
$config->setModulesDir(__DIR__.'/modules/'); // Default modules path
// Prompt Console / Dump
$config->setVerbose(true); // Prompt verbose output
$config->setJson(false); // Prompt json output
// Modules Activation
$config->enableDefaultModule(true); // Enable default SEO kernel modules

// Url
$url = new Url('http://marquand.pro');
// Spider
$spider = new Spider($url, $config);
$result = $spider->run();

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
$config->enableDefaultModule(true); // Enable default SEO kernel modules
$config->removeModule('FileDownload'); // Disable Module

// Url
$url = new \Mediashare\Spider\Entity\Url('https://mediashare.fr/');

// Spider
$spider = new \Mediashare\Spider\Spider($url, $config);
$result = $spider->run();
// dump($result);