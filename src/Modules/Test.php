<?php
namespace App\Modules;

class Test {
    public $name = "Test";
    public $description = "Ce module est un test By Slote";
    public $config;
    public $webpage; // Headers & Body
    public $dom; // Dom for crawl in webpage
    public $variables = [""]; // Variables injected
    public $errors; // Output errors
    
     public function run() { return "Hello Webspider!"; } qsd
}