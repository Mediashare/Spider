<?php
namespace Spider\Modules;

class Evaneos {
    public $name = "Evaneos";
    public $description = "";
    public $config;
    public $webpage; // Headers & Body
    public $dom; // Dom for crawl in webpage
    public $variables = false; // Variables injected
    public $errors; // Output errors
    
    public function run () {
        $page_content = $this->webpage->getBody()->getContent();
        if (strpos($page_content, '/iframe/widget-evaneos.php') !== false):
            return 'Iframe Evaneos Widget Here!';
        endif;
        foreach ($this->dom->filter('script') as $script) {
            $content = (string) $script->textContent;
            if (strpos($content, 'EvaneosWidgetsObject') !== false):
                return 'Script Evaneos Widget Here!';
            endif;
        }
    }
}