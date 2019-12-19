
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
// Prompt Console / Output
$config->setJson(false); // Prompt json output
$config->setHtml(true); // Prompt html output
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
```log
--- (1) URL: [http://marquand.pro] 140ms --- 
--- (2) URL: [http://marquand.pro/projets] 67ms --- 
--- (3) URL: [http://marquand.pro/CurriculumVitae] 58ms --- 
--- (4) URL: [http://marquand.pro/contact] 68ms --- 
--- (5) URL: [http://marquand.pro/12/projet] 70ms --- 
--- (6) URL: [http://marquand.pro/10/projet] 64ms --- 
--- (7) URL: [http://marquand.pro/8/projet] 63ms --- 
--- (8) URL: [http://marquand.pro/7/projet] 65ms --- 
--- (9) URL: [http://marquand.pro/getmonero.org] 45ms --- 
--- (10) URL: [http://marquand.pro/6/projet] 64ms --- 
--- (11) URL: [http://marquand.pro/4/projet] 70ms --- 
--- (12) URL: [http://marquand.pro/projets/#] 84ms --- 
--- (13) URL: [http://marquand.pro/projets/getmonero.org] 63ms --- 
--- (14) URL: [http://marquand.pro/CurriculumVitae/#] 59ms --- 
--- (15) URL: [http://marquand.pro/contact/#] 71ms --- 
--- (16) URL: [http://marquand.pro/12/projet/#] 60ms --- 
--- (17) URL: [http://marquand.pro/10/projet/#] 61ms --- 
--- (18) URL: [http://marquand.pro/8/projet/#] 61ms --- 
--- (19) URL: [http://marquand.pro/7/projet/#] 63ms --- 
--- (20) URL: [http://marquand.pro/7/projet/getmonero.org] 51ms --- 
--- (21) URL: [http://marquand.pro/6/projet/#] 63ms --- 
--- (22) URL: [http://marquand.pro/4/projet/#] 61ms --- 
**********************
* Output file result: /home/slote/Bureau/Spider/var/reports/marquand.pro/test.json
**********************
```