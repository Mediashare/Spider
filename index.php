<?php
require 'vendor/autoload.php';
use Mediashare\Spider\Entity\Config;
use Mediashare\Spider\Entity\Url;
use Mediashare\Spider\Spider;

// Website Config
$config = new Config();
$config->setWebspider(false); // Crawl all website
// $config->setPathRequires(['/Kernel/']); // Not crawl other path
// $config->setPathExceptions(['/CodeSnippet/']); // Not crawl this path
// Modules
$config->setReportsDir(__DIR__.'/reports/'); // Default reports path
$config->setModulesDir(__DIR__.'/modules/'); // Default modules path
$config->enableDefaultModule(false); // Enable default SEO kernel modules
$config->removeModule('FileDownload'); // Disable Module
// Prompt Console / Dump
$config->setVerbose(true); // Prompt verbose output
$config->setJson(false); // Prompt json output

// Url
$url = new Url('https://marquand.pro');

// Run Spider
$spider = new Spider($url, $config);
$result = $spider->run();