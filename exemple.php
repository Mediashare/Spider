<?php
require 'vendor/autoload.php';

$url = 'http://marquand.pro';
$options = [
    'id' => 'audit-marquand.pro', // Id|Name report (uniqid() by default)
    'webspider' => true, // Crawl all website
    'require' => [], // Path required
    'exception' => [], // Path exceptions
    'html' => true, // Prompt html output
    'json' => false,  // Prompt json output
    'reports_dir' => __DIR__.'/reports/', // Default reports path
    'modules_dir' => __DIR__.'/src/Modules/', // Default modules path
    'modules' => ['Links'], // Select one or more modules to use with class name
    'enable_modules' => true, // Enable all modules
];

$config = new \Mediashare\Entity\Config();
// $config->setId("Audit");
$config->setUrl('http://marquand.pro');
$config->setWebspider(true);
// Require & Exception in URL
$config->setRequires([]);
$config->setExceptions([]);
// Output
$config->setReportsDir(__DIR__.'/reports/');
$config->setModulesDir(__DIR__.'/src/Modules/');
$config->setJson(false);
$config->setHtml(true);
$config->enableAllModule(false);
// Modules
$config->addModules(['Links', 'Search']);
// Inject this variables in modules 
$config->addVariables(['Search' => ['Thibault']]);

$spider = new \Mediashare\Spider($config);
$result = $spider->run();
// dump($result);

