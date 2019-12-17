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
    public $reports_dir = __DIR__.'/../var/reports/';
    public $modules_dir = __DIR__.'/Modules/';

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
        if (!empty($option['id'])): $this->set('id', $option['id']); endif;
        if (!empty($option['webspider'])): $this->set('webspider', $option['webspider']); endif;
        if (!empty($option['require'])): $this->set('require', $option['require']); endif;
        if (!empty($option['exception'])): $this->set('exception', $option['exception']); endif;
        if (!empty($option['json'])): $this->set('json', $option['json']); endif;
        if (!empty($option['output'])): $this->set('output', $option['output']); endif;
        if (!empty($option['modules_dir'])): $this->set('modules_dir', $option['modules_dir']); endif;
        if (!empty($option['reports_dir'])): $this->set('reports_dir', $option['reports_dir']); endif;
        if (!empty($option['modules'])): $this->set('modules', $option['modules']); endif;
        if (!empty($option['all_modules'])): $this->set('all_modules', $option['all_modules']); endif;
        $this->config = $this->initConfig();
    }

    public function run() {
        $webspider = new Webspider();
        $webspider->run($this->config);
    } 

    public function initConfig() {
        $config = new Config();
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

