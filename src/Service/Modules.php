<?php
namespace Mediashare\Spider\Service;

use Mediashare\Kernel\Kernel;
use Mediashare\Crawler\Crawler;
use Mediashare\Spider\Entity\Config;
use Mediashare\Spider\Service\Output;
use Mediashare\ModulesProvider\Modules as AnotherModules;
use Mediashare\ModulesProvider\Config as configModules;

class Modules {
    public $crawler;
    public $modules = [];
    public function __construct(Crawler $crawler, Config $config) {
        $this->crawler = $crawler;
        $this->config = $config;
        $this->output = new Output($config);
        $this->initModules();
    }

    public function run() {
        $results = [];
		$counter = 0;
		foreach ($this->modules as $name => $module) {
            $counter++;
            $this->output->progressBar($counter, count($this->modules), "[Module Runing] ".$module->name);
            foreach ($this->crawler->urls as $url => $data) {
                $module->url = $url;
                $module->config = $this->config;
                $module->dom = $data->dom;
                $module->links = $data->webpage->links;
                $module->body = $data->webpage->getBody()->getContent();
                $results[$module->name][$url] = $module->run();
                if (!empty($module->errors)):
                    $this->errors[$module->name][$url] = $module->errors;
                endif;
                $data->webpage->getBody()->setContent(""); // Reset body content for memory optimization.
            }
		}
		return $results;
    }

    /**
	 * Init Modules
	 */
	public function initModules() {
        // Default Modules (Kernel SEO)
        $this->modules = $this->config->getModules(); 
        // Another Modules
        $config = new configModules();
        $config->setModulesDir($this->config->getModulesDir());
        $config->setNamespace("Mediashare\\Modules\\");
        $modules = new AnotherModules($config);
        foreach ($modules->modules as $module) {
            $this->modules[$module->name] = $module;
        }
	}
}