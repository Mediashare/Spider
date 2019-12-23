# Modules
Adding a module to a crawler allows the automation of code execution on one or more pages of a website. Modules are executed when crawling a page.
Create your own Module for execute php code for webpage crawled and get output result in your json report file.
This project use [mediashare/modules-provider](https://github.com/Mediashare/modules-provider) library. Modules Provider is an object autoloader for automating and simplifying some code inclusion in different projects.

## Basic Usage
### Exemple
```php
<?php
// ./modules/Hello.php
namespace Mediashare\Modules;
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
```

## Use DomCrawler
### Create own module to execute actions when the crawler scraps a webpage. 
#### Requierements
- The name of your class needs to be the same as the name of the .php file.
- The entry point for executing modules is the run() function, so it is mandatory to have a run() function in your module.
  
Spider executes the run() function public when the webpage has just been crawled. So you can use the DomCrawler.
### Documentation
DomCrawler is symfony component for DOM navigation for HTML and XML documents. You can retrieve [Documentation Here](https://symfony.com/doc/current/components/dom_crawler.html#usage).
### Exemple
```php
// ./modules/Links.php
namespace Mediashare\Modules;
/**
 * Links
 * Get all links in webpage
 */
class Links {
    public $config; // Spider Config
    public $url; // Url with Headers & Body
    public $crawler; // DomCrawler
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
```

## Output Errors
```php
// ./modules/OutputErrors.php
namespace Mediashare\Modules;

class OutputErrors {
    public $config; // Spider Config
    public $url; // Url with Headers & Body
    public $crawler; // DomCrawler
    public $errors; // Output errors
    
    public function run() { 
        $this->errors[] = [
            'type' => 'SEO',
            'message' => 'Title not found!',
            'url' => (string) $this->webpage->getUrl(),
        ];
        return "Hello Webspider!"; // Is output result in report 
    }
}
```