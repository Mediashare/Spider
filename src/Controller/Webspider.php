<?php
namespace Mediashare\Spider\Controller;

use Mediashare\Spider\Entity\Url;
use Mediashare\Spider\Entity\Config;
use Mediashare\Spider\Entity\Result;
use Mediashare\Spider\Service\Output;
use Mediashare\ModulesProvider\Modules;
use Mediashare\ModulesProvider\Config as ModuleConfig;
use Mediashare\Crawler\Crawler;
use Mediashare\Crawler\Config as CrawlerConfig;


/**
 * WebSpider
 */
class Webspider
{
	public $url;
	public $config;
	public $crawler;
	public $modules = [];
	public $errors = [];
	public function __construct(Url $url, Config $config) {
		$this->url = $url;
		$this->config = $config;
		$this->output = new Output($config);
		$config->setUrl($url);
	}

	public function run() {
		// Banner
		$this->output->banner();
		// Crawler
		$this->crawler = $this->crawl();
		// Modules
		$this->modules = $this->modules($this->crawler);
		// Result
		$result = new Result($this);
		$result->build();
		
		// Output
		$json = $result->json($this);
		$this->output->json($json);
		$this->output->fileDirection($this->config->getOutput());
		
		return $this;
	}

	public function crawl() {
		$config = new CrawlerConfig();
		$config->setVerbose($this->config->getVerbose());
		$crawler = new Crawler((string) $this->url, $config);
		$crawler->run();
		return $crawler;
	}
	
	/**
	 * Execute Another Modules
	 *
	 * @param Crawler $crawler
	 * @return array
	 */
	public function modules(Crawler $crawler) {
		$results = [];
		$config = new ModuleConfig();
		$config->setModulesDir($this->config->getModulesDir());
		$config->setNamespace("Mediashare\\Modules\\");
		$modules = new Modules($config);
		$modules = $modules->getModules();
		foreach ($crawler->urls as $url => $data) {
			foreach ($modules as $module) {
				if ($module->name != "FileDownload"):
					$module->url = $url;
					$module->config = $this->config;
					$module->dom = $data->dom;
					$module->links = $data->webpage->links;
					$module->body = $data->webpage->getBody()->getContent();
					$results[$module->name][$url] = $module->run();
					if (!empty($module->errors)):
						$this->errors[$module->name][] = $module->errors;
					endif;
				endif;
			}
			$data->webpage->getBody()->setContent(""); // Reset body content for memory optimization.
		}
		return $results;
	}
}