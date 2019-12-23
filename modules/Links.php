<?php
namespace Mediashare\Modules;

/**
 * Links
 * Get all links in webpage
 */
class Links {
    public $dom;
    public function run() { 
        $links = [];
        foreach($this->dom->filter('a') as $link) {
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