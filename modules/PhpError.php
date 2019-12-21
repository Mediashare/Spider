<?php
namespace Mediashare\Spider\Modules;

class PhpError {
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