<?php
namespace Mediashare\Spider\Service;

use Mediashare\Kernel\Kernel;
use Mediashare\Scraper\Scraper;
use Mediashare\Spider\Entity\Config;
use Mediashare\Spider\Service\Output;
use Mediashare\ModulesProvider\Config as configModules;
use Mediashare\ModulesProvider\Modules as AnotherModules;

class Modules {
    public $modules = [];
    public function __construct(Config $config) {
        $this->config = $config;
        $this->output = new Output($config);
        $this->initModules();
    }

    public function run(Scraper $scraper) {
        $results = [];
        $counter = 0;
        $url = (string) $scraper->webpage->getUrl();
		foreach ($this->modules as $name => $module) {
            $counter++;
            $this->output->progressBar($counter, count($this->modules), "[Module Runing] ".$module->name);
            $module->url = $url;
            $module->config = $this->config;
            $module->dom = $scraper->dom;
            $module->links = $scraper->webpage->links;
            $module->body = $scraper->webpage->getBody()->getContent();
            $module->header = $scraper->webpage->getHeader();
            $results[$module->name][] = $module->run();
            if (!empty($module->errors)):
                $results[$module->name]['errors'] = array_merge($results['errors'] ?? [], $module->errors ?? []);
            endif;
            $scraper->webpage->getBody()->setContent(""); // Reset body content for memory optimization.
		}
		return $results;
    }

    /**
	 * Init Modules
	 */
	public function initModules() {
        // Default Modules (Kernel SEO)
        if ($this->config->enableDefaultModules):
            $this->modules = $this->config->getModules(); 
        endif;
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