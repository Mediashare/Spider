<?php
namespace Mediashare\Modules;

class Search {
    public $name = "Search";
    public $description = "Search text from a webpage. 
This command need injection variables with text(s) do you want find in website.";
    public $config;
    public $webpage; // Headers & Body
    public $crawler; // Dom for crawl in webpage
    public $variables = true; // Variables injected
    public $errors; // Output errors
    
    public function run() { 
        $results = [];
		foreach ((array) $this->variables as $search) {
			if (!empty($search)) {
                $search = strtolower($search);
                $text = strtolower($this->crawler->text());
				if (strpos($text, $search) !== false || 
					strpos(strip_tags($text), strip_tags($search)) !== false) {
					// Write result
					$results['Search: '.$search] = [
						'url' => (string) $this->webpage->getUrl()
					];
				}
			}
        }
        return $results;
    }
}