<?php
namespace Mediashare\Spider\Modules;

class PhpError {
    public $config;
    public $url; // Url with Headers & Body
    public $crawler; // Dom for crawl in webpage
    public $variables = false; // Variables injected
    public $errors; // Output errors
    
    public function run() {
    	$html = $this->url->getWebpage()->getBody()->getContent();
    	$errors[] = "/Applications/MAMP/htdocs/";
    	foreach ($errors as $error):
    		if (strpos($html, $error)):
    			return [
    				'Error' => true,
    				'url' => $this->url->getWebpage()->getUrl()->getUrl()
    			];
    		endif;
    	endforeach;
    }
}