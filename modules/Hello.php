<?php
namespace Mediashare\Modules;

/**
 * Hello
 * @description Return Hello World! From [url source]
 * @return string 
 */
class Hello {
    public $url;
    public function run() { 
        return "Hello World! From [".$this->url."]";
    }
}