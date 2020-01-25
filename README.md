# Spider
:dizzy: Spider is a PHP command line tool that allows you to crawl a website for informations scraping.

Spider is a crawler of website modulable write in PHP.
The tool allows you to retrieve information and execute code on website pages. It can be useful for SEO or security audit purposes.
Users have the possibility to use the modules created by the community or to create their own modules (written in PHP via a web interface).

### What is a Crawler?
A crawler is an indexing robot, it automatically explores the pages of a website.
Using a crawler can have several interests:
- Information search & retrieval
- Validation of the SEO of your website
- Integration test
- Execution of PHP code on several pages in an automated way

### Features
  - Get all links from website
  - Check HTTP response
  - Create your own [**Modules**](##modules) (Crawl & execute your PHP code).
  - No database, Pure PHP & Symfony
  - Output json file

### Libraries
  - Autoloader php class for code integration easily. [mediashare/modules-provider](https://packagist.org/packages/mediashare/modules-provider)
  - Website bot crawler. [mediashare/crawler](https://packagist.org/packages/mediashare/crawler)
  - Scraper with DomCrawler integration. [mediashare/scraper](https://packagist.org/packages/mediashare/scraper)

I would be happy to receive your ideas and contributions to the project :smiley:

## Getting started
### Installation
#### [Composer Usage](https://packagist.org/packages/Mediashare\Spider/spider)
Use Spider library in your project & create your own modules. 
```bash
composer require mediashare/spider
```

## Usage
### Create index.php file and init the config.
```php
<?php
// ./index.php
require 'vendor/autoload.php';
use Mediashare\Spider\Entity\Config;
use Mediashare\Spider\Entity\Url;
use Mediashare\Spider\Spider;

// Website Config
$config = new Config();
$config->setWebspider(true); // Crawl all website
$config->setReportsDir(__DIR__.'/reports/'); // Default reports path
$config->setModulesDir(__DIR__.'/modules/'); // Default modules path
// Prompt Console / Dump
$config->setVerbose(true); // Prompt verbose output
$config->setJson(false); // Prompt json output
// Modules Activation
$config->enableDefaultModule(true); // Enable default SEO kernel modules
$config->removeModule('FileDownload'); // Disable Module

// Url
$url = new Url('https://mediashare.fr');

// Run Spider
$spider = new Spider($url, $config);
$result = $spider->run();
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

## Modules
### Requierements
- The name of your class needs to be the same as the name of the .php file.
- The entry point for executing modules is the run() function, so it is mandatory to have a run() function in your module.
Spider executes the run() function public when the webpage has just been crawled. So you can use the [DomCrawler](https://symfony.com/doc/current/components/dom_crawler.html#usage).
### Documentation
DomCrawler is symfony component for DOM navigation for HTML and XML documents. You can retrieve [Documentation Here](https://symfony.com/doc/current/components/dom_crawler.html#usage).
### Create own module to execute actions when the crawler scraps a webpage. 
```php
<?php
// ./modules/LinksTest.php
namespace Mediashare\Modules;

class LinksTest {
    public $dom;
    public function run() { 
        $links = [];
        foreach($this->dom->filter('a') as $link) {
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
## [Modules](https://packagist.org/packages/mediashare/modules-provider)
Modules are tools created by the community to add features when crawling a website.
Adding a module to a crawler allows the automation of code execution on one or more pages of a website. Modules are executed when crawling a page.
[More information...](https://packagist.org/packages/mediashare/modules-provider)
