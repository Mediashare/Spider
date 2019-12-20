<?php
namespace Mediashare\Modules;

class PhpError {
    public $name = "PhpError";
    public $description = "";
    public $config;
    public $webpage; // Headers & Body
    public $crawler; // Dom for crawl in webpage
    public $variables = false; // Variables injected
    public $errors; // Output errors
    
    public function run() {
    	$html = $this->webpage->getBody()->getContent();
    	$errors[] = "/Applications/MAMP/htdocs/";
    	foreach ($errors as $error):
    		if (strpos($html, $error)):
    			return [
    				'Error' => true,
    				'url' => $this->webpage->getUrl()->getUrl()
    			];
    		endif;
    	endforeach;
    }
}