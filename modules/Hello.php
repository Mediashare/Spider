<?php
namespace Mediashare\Spider\Modules;

/**
 * Hello
 * @description Return Hello World! From [url source]
 * @return string 
 */
class Hello {
    public function run() { 
        return "Hello World! From [".$this->url->getUrl()."]";
    }
}