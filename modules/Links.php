<?php
namespace Mediashare\Spider\Modules;

use Mediashare\Spider\Entity\Url;

class Links {
    public $name = "Links";
    public $description = "Get all links in webpage";
    public $url; // Url with Headers & Body
    public $crawler; // Dom for crawl in webpage
    public $errors; // Output errors
    
    public function run() { 
        $source = $this->url->getUrl();
        $links = [];
        foreach($this->crawler->filter('a') as $link) {
            if (!empty($link)) {
                $href = rtrim(ltrim($link->getAttribute('href')));
                if ($href) {
                    if (isset($links[$href])) {
                        $links[$href]['counter']++;
                    } else {
                        $links[$href]['counter'] = 1;
                    }
                }
            }
        }
        return $links;
    }
}