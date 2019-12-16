# Modules
Modules are tools created by the community to add features when crawling a website.
Adding a module to a crawler allows the automation of code execution on one or more pages of a website. Modules are executed when crawling a page.

Create your own Module for execute php code for webpage crawled and get output result in your json report file.
#### Create module
```
php bin/console spider:module:create "Module Name"
```
#### Module list
```bash
php bin/console spider:module:list
```
#### Enable all modules
```bash
php bin/console spider:run http://exemple.com -w -m
```
#### Enable specific modules
```bash
php bin/console spider:run http://exemple.com -w -m Links -m Search -m NewModule
```
#### Disable specific modules
```bash
php bin/console spider:run http://exemple.com -w -m Links -m Search -m NewModule -d FileDownload
```
#### Inject json variables in module
```bash
php bin/console spider:run http://exemple.com -i '{"Search":{"value search"}}' -i '{"Search":{"value search 2"}}'
```


Now you can write your php code in function run() from Module file created.

## Basic
### Exemple
```php
// src/Modules/ModuleName.php
namespace App\Modules;

class ModuleName {
    public $name = "Module Name";
    public $description = "This is description of module.";
    public $config;
    public $webpage; // Headers & Body
    public $dom; // Dom for crawl in webpage
    public $variables; // Variables injected
    public $errors; // Output errors
    
    public function run() { 
        return "Hello Webspider!"; // Is output result in report 
    }
}
```

## Variable injection
Catch injected variables in your module. 
```php
namespace App\Modules;

class InjectVariables {
    public $name = "InjectVariables";
    public $description = "Vavirables injection in module.";
    public $config;
    public $webpage; // Headers & Body
    public $dom; // Dom for crawl in webpage
    public $variables; // Variables injected
    public $errors; // Output errors
    
    public function run() { 
      $variables = $this->getVariables();  
      return $variables;
    }

    private function getVariables() {
      $variables = [];
      foreach ((array) $this->variables as $varName => $value) {
          $variables[$varName] = $value;
      }
      return $variables;
    }
}
```
```bash
php bin/console spider:run http://exemple.com --inject-variables '{"InjectVariables":{"varName": "value"}}'
```

## Use DomCrawler
### Documentation
DomCrawler is symfony component for DOM navigation for HTML and XML documents. You can retrieve documentation [Here](https://symfony.com/doc/current/components/dom_crawler.html#usage).
### Exemple
```php
// src/Modules/Links.php
namespace App\Modules;

class Links {
    public $name = "Links";
    public $description = "Get all links from a webpage.";
    public $webpage; // Headers & Body
    public $dom; // DomCrawler for crawl in webpage
    public $variables; // Variables injected
    public $errors; // Output errors

    public function run() {
        $source = $this->webpage->getUrl();
        $links = [];
        foreach($this->dom->filter('a') as $link) {
            if (!empty($link)) {
                $href = $link->getAttribute('href');
                if ($href) {
                    $links[(string) $source][$href] = [
                        'href' => $href
                    ];
                }
            }
        }
        return $links;
    }
}
```

## Output Errors
```php
// src/Modules/ModuleName.php
namespace App\Modules;

class OutputErrors {
    public $name = "Output Errors";
    public $description = "Output errors from Module";
    public $config;
    public $webpage; // Headers & Body
    public $dom; // Dom for crawl in webpage
    public $variables; // Variables injected
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