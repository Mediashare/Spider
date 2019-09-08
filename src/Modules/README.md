# Modules
Create your own Module for execute php code for webpage crawled and get output result in your json report file.

## Modules list
```bash
php bin/console webspider:module:list
```
## Create Module
```bash
php bin/console webspider:module:create "Module Name"
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

## Output
Report is write in `` public/reports/exemple.com/report.json ``
```JavaScript
{
  "website": {
    "domain": "exemple.com",
    "scheme": "http"
  },
  "config": {
    "webspider": false,
    "pathRequire": {...},
    "pathException": {...},
    "json": false,
    "output": null,
    "variables-injected": {
      "moduleName": {
        "varName": "foo bar",
        ...
      },
      ...
    }
  }
  "urls": {
    "http://exemple.com": {
      "header": {
        "httpCode": 200,
        "transferTime": 0.125422,
        "downloadSize": 38296,
        "headers": {...} // Other data header
      }
    }
    ...
  },
  "modules": {
    "Module Name": {
      "http://exemple.com": {
        "name": "Module 1",
        "results": {...}
      },
      ...
    },
    ...
  "errors": {...}
  }
}
```