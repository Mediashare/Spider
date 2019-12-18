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
    public $disable_modules = false; // Disable all modules
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

    public function __construct(string $url, ?array $option) {
        $this->id = uniqid();
        $this->set('url', $url);
        if (isset($option['id'])): $this->set('id', $option['id']); endif;
        if (isset($option['webspider'])): $this->set('webspider', $option['webspider']); endif;
        if (isset($option['require'])): $this->set('require', $option['require']); endif;
        if (isset($option['exception'])): $this->set('exception', $option['exception']); endif;
        if (isset($option['prompt']['html'])): $this->set('html', $option['prompt']['html']); endif;
        if (isset($option['prompt']['json'])): $this->set('json', $option['prompt']['json']); endif;
        if (isset($option['modules'])): $this->set('modules', $option['modules']); endif;
        if (isset($option['enable_modules'])): $this->set('enable_modules', $option['enable_modules']); endif;
        if (isset($option['disable_modules'])): $this->set('disable_modules', $option['disable_modules']); endif;
        if (isset($option['modules_dir'])): $this->set('modules_dir', $option['modules_dir']); endif;
        if (isset($option['reports_dir'])): $this->set('reports_dir', $option['reports_dir']); endif;
        if (isset($option['inject_variables'])): $this->set('inject_variables', $option['inject_variables']); endif;
        if (isset($option['output'])): $this->set('output', $option['output']); endif;
        $this->config = $this->initConfig();
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
    
        return $config;
    }
}

