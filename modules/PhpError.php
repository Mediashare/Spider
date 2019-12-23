<?php
namespace Mediashare\Modules;

class PhpError {
	public $url;
	public $body;
    public function run() {
    	$errors[] = "/Applications/MAMP/htdocs/";
    	foreach ($errors as $error):
    		if (strpos($this->body, $error)):
				$error = [
					'Error' => true,
					'url' => $this->url
				];
				$this->errors = $error;
				return $error;
    		endif;
    	endforeach;
    }
}