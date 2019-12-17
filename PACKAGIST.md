
# Packagist Version
## Installation
```bash
composer require medishare/spider
```

## Usage
### Create index.php file and init the config.
```php
<?php
// ./index.php
require 'vendor/autoload.php';

$url = 'http://marquand.pro';
$options = [
    'id' => 'test', // Id|Name report
    'webspider' => true, // Crawl all website
    'require' => [], // Path required
    'exception' => [], // Path exceptions
    'prompt' => [ // Prompt options
        'html' => true, // Html output
        'json' => false,  // Json output
    ],
    'modules_dir' => __DIR__.'/modules/', // Default modules path
    'reports_dir' => __DIR__.'/var/reports/', // Default reports path
    'all_modules' => false, // Enable all modules
    'disable_modules' => false, // Disable all modules
    'modules' => ['Links'] // Select one or more modules to use with class name
];

$spider = new \Spider\Spider($url, $options);
$result = $spider->run();
// dump($result);
```

### Create own module to execute actions when the crawler scraps a webpage. 
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
### Execute the code from the console.
```bash
php index.php
```
