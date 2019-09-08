<?php
namespace App\Modules;

class Links {
    public $name = "Links";
    public $description = "Get all links from a webpage.";
    public $config;
    public $webpage; // Headers & Body
    public $dom; // Dom for crawl in webpage
    public $variables = false; // Variables injected
    public $errors; // Output errors

    public function run() {
        $source = $this->webpage->getUrl();
        $links = [];
        foreach($this->dom->filter('a') as $link) {
			if (!empty($link)) {
				$href = rtrim(ltrim($link->getAttribute('href')));
				if ($href) {
                    if (isset($links[$href])) {
                        $links[$href]++;
                    } else {
                        $links[$href] = 1;
                    }
				}
			}
        }
        return $links;
    }
}
