<?php
namespace Mediashare;

use Mediashare\Entity\Url;
use Mediashare\Entity\Config;
use Mediashare\Entity\Website;
use Mediashare\Service\Output;
use Mediashare\Controller\Webspider;
session_start();

class Spider
{
    public $id; // Id|Name report
    public $url = "http://marquand.pro";
    public $webspider = true; // Crawl all website
    public $require = []; // Path required
    public $exception = []; // Path exceptions
    public $html = false; // Prompt html output
    public $json = false; // Prompt json output
    public $modules = []; // Select one or more modules to use
    public $enable_modules = true; // Enable all modules
    public $modules_dir = __DIR__.'/Modules/'; // Default modules path
    public $reports_dir = __DIR__.'/../var/reports/'; // Default reports path
    public $inject_variables = [];
    public $output = null; // Rewrite ouput destination

    public function set(string $input, $value) {
        $this->$input = $value;
        return $this;
    }
    public function get($input) {
        return $this->$input;
    }

    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function run() {
        $webspider = new Webspider($this->config);
        $reports = $webspider->run();
        return $reports;
    } 

    public function initConfig() {
        $config = new Config();
        $config->setId($this->get('id'));
        $config->addUrls([$this->get('url')]);
        $config->setWebspider($this->get('webspider'));
        // Require & Exception in URL
        $config->setRequires((array) $this->get('require'));
        $config->setExceptions((array) $this->get('exception'));
        // Output
        $config->setReportsDir($this->get('reports_dir'));
        $config->setModulesDir($this->get('modules_dir'));
        $config->setJson($this->get('json'));
        $config->setOutput($this->get('output'));
        $config->setHtml($this->get('html'));
        $config->enableAllModule($this->get('enable_modules'));
        // Modules
        $config->addModules($this->get('modules'));
        // Inject this variables in modules 
        $config->addVariables($this->get('inject_variables'));
        // dump($config);die;
        return $config;
    }
}

