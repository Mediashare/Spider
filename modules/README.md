# Modules
Modules are tools created by the community to add features when crawling a website.
Adding a module to a crawler allows the automation of code execution on one or more pages of a website. Modules are executed when crawling a page.

Create your own Module for execute php code for webpage crawled and get output result in your json report file.


## Basic Usage
### Exemple
```php
<?php
// ./modules/Hello.php
namespace Mediashare\Spider\Spider\Modules;

class Hello {
    public $name = "Hello";
    public $description = "Return Hello World! From [url source]";
    public $config; // Spider Config
    public $url; // Url with Headers & Body
    public $crawler; // Dom for crawl in webpage
    public $errors; // Output errors
    
    public function run() { 
        return "Hello World! From [".$this->url->getUrl()."];
    }
}
```

## Use DomCrawler
### Documentation
DomCrawler is symfony component for DOM navigation for HTML and XML documents. You can retrieve documentation [Here](https://symfony.com/doc/current/components/dom_crawler.html#usage).
### Exemple
```php
// ./modules/Links.php
namespace Mediashare\Spider\Modules;
/**
 * Links
 * Get all links in webpage
 */
class Links {
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
```

## Output Errors
```php
// ./modules/OutputErrors.php
namespace Mediashare\Spider\Spider\Modules;

class OutputErrors {
    public $name = "Output Errors";
    public $description = "Output errors from Module";
    public $config; // Spider Config
    public $url; // Url with Headers & Body
    public $crawler; // Dom for crawl in webpage
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