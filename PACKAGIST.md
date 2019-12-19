
# Packagist Version
## Installation
```bash
composer require mediashare/spider
```

## Usage
### Create index.php file and init the config.
```php
<?php
// ./index.php
require 'vendor/autoload.php';

// Website Config
$config = new \Mediashare\Entity\Config();
$config->setWebspider(true); // Crawl all website
$config->setReportsDir(__DIR__.'/reports/'); // Default reports path
$config->setModulesDir(__DIR__.'/modules/'); // Default modules path
// Prompt Console / Dump
$config->setVerbose(true); // Prompt verbose output
$config->setJson(false); // Prompt json output
// Modules Activation
$config->enableAllModule(true); // Enable all modules
// Modules Activation
$config->enableAllModule(true); // Enable all modules
// $config->addModules(['Links', 'Search']);// Select one or more modules to use with class name

// Url
$url = new \Mediashare\Entity\Url('http://marquand.pro');
// Spider
$spider = new \Mediashare\Spider($url, $config);
$result = $spider->run();
// dump($result);
```

### Create own module to execute actions when the crawler scraps a webpage. 
```php
// ./modules/Links.php
<?php
namespace Mediashare\Modules;

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
#### Output
```sh
-*--*--*--*--*--*--*--*--*--*--*--*--*--*--*--*--*
* Output file result: /home/slote/Bureau/Spider/var/reports/marquand.pro/5dfaf1c0147c6.json
-*--*--*--*--*--*--*--*--*--*--*--*--*--*--*--*--*
```