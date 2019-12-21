<?php
namespace Mediashare\Modules;

class Evaneos {
    public $name = "Evaneos";
    public $description = "";
    public $config;
    public $url; // Url with Headers & Body
    public $crawler; // Dom for crawl in webpage
    public $variables = false; // Variables injected
    public $errors; // Output errors
    
    public function run () {
        $page_content = $this->url->getWebpage()->getBody()->getContent();
        if (strpos($page_content, '/iframe/widget-evaneos.php') !== false):
            return 'Iframe Evaneos Widget Here!';
        endif;
        foreach ($this->crawler->filter('script') as $script) {
            $content = (string) $script->textContent;
            if (strpos($content, 'EvaneosWidgetsObject') !== false):
                return 'Script Evaneos Widget Here!';
            endif;
        }
    }
}