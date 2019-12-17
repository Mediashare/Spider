
# Packagist Version
## Installation
```bash
composer require medishare/spider
```

## Usage
```php
<?php
// ./index.php
require 'vendor/autoload.php';

$url = 'http://marquand.pro';
$input = [
    'id' => 'test',
    'webspider' => true,
    'json' => true, 
    'reports_dir' => __DIR__.'/reports/',
    'modules_dir' => __DIR__.'/modules/',
];
$spider = new \Spider\Spider($url, $input);
$spider->run();
```

## Modules
Create own module to execute actions when the crawler scraps a webpage. 
```php
// ./modules/Links.php
<?php
namespace Spider\Modules;

class Links {
    public $name = "Links";
    public $description = "Get all links in webpage";
    public $config;
    public $webpage; // Headers & Body
    public $dom; // Dom for crawl in webpage
    public $variables = "0"; // Variables injected
    public $errors; // Output errors
    
    public function run() { 
        $source = $this->webpage->getUrl();
        $links = [];
        foreach($this->dom->filter('a') as $link) {
            if (!empty($link)) {
                $href = rtrim(ltrim($link->getAttribute('href')));
                if ($href) {
                    if (isset($links[$href])) {
                        $links[$href]++;
                    } else {
                        $links[$href] = 1;
                    }
                }
            }
        }
        return $links;
    }
}
```