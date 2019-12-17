<?php
namespace Spider;

use Spider\Entity\Url;
use Spider\Entity\Config;
use Spider\Entity\Website;
use Spider\Controller\Output;
use Spider\Controller\Webspider;
session_start();

class Spider
{
    public $id;
    public $url = "http://marquand.pro";
    public $webspider = true;
    public $require = [];
    public $exception = [];
    public $json = false;
    public $output = null;
    public $html = true;
    public $modules = [];
    public $all_modules = true;
    public $disable_modules = false;
    public $modules_dir = __DIR__.'/Modules/';
    public $reports_dir = __DIR__.'/../var/reports/';

    public function set(string $input, $value) {
        $this->$input = $value;
        return $this;
    }
    public function get(string $input) {
        return $this->$input;
    }

    public function __construct(string $url, ?array $option) {
        $this->id = uniqid();
        $this->set('url', $url);
        if (isset($option['id'])): $this->set('id', $option['id']); endif;
        if (isset($option['webspider'])): $this->set('webspider', $option['webspider']); endif;
        if (isset($option['require'])): $this->set('require', $option['require']); endif;
        if (isset($option['exception'])): $this->set('exception', $option['exception']); endif;
        if (isset($option['json'])): $this->set('json', $option['json']); endif;
        if (isset($option['output'])): $this->set('output', $option['output']); endif;
        if (isset($option['html'])): $this->set('html', $option['html']); endif;
        if (isset($option['modules'])): $this->set('modules', $option['modules']); endif;
        if (isset($option['all_modules'])): $this->set('all_modules', $option['all_modules']); endif;
        if (isset($option['disable_modules'])): $this->set('disable_modules', $option['disable_modules']); endif;
        if (isset($option['modules_dir'])): $this->set('modules_dir', $option['modules_dir']); endif;
        if (isset($option['reports_dir'])): $this->set('reports_dir', $option['reports_dir']); endif;
        $this->config = $this->initConfig();
    }

    public function run() {
        $webspider = new Webspider();
        $webspider->run($this->config);
    } 

    public function initConfig() {
        $config = new Config();

        if ($this->get('id')) {
            $config->setId($this->get('id'));
        }

        foreach ((array) $this->get('url') as $newUrl) {
            $url = new Url($newUrl);
            $config->addUrl($url);
            
            $website = $config->getWebsite($url);
            if ($website) {
                $website->addUrl($url);
            } else {
                $website = new Website($url);
                $config->addWebsite($website);
            }
        }
    
        $config->setWebspider($this->get('webspider'));
        // Require & Exception in URL
        $config->setPathRequire((array) $this->get('require'));
        $config->setPathException((array) $this->get('exception'));
        // Output
        $config->reportsDir = $this->get('reports_dir');
        $config->modulesDir = $this->get('modules_dir');
        $config->json = $this->get('json');
        $config->output = $this->get('output');
        $config->html = $this->get('html');
        // Modules
        $config->modules = $this->get('modules');
        $config->all_modules = $this->get('all_modules');
        $config->disable_modules = $this->get('disable_modules');
        // Inject input variables in modules 
        $config->variables = null;
    
        return $config;
    }
}

