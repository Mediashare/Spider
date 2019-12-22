<?php
namespace Mediashare\Spider\Controller;

use Mediashare\Crawler\Crawler;
use Mediashare\Spider\Entity\Url;
use Mediashare\Spider\Entity\Config;
use Mediashare\Spider\Entity\Result;
use Mediashare\Spider\Service\Output;
use Mediashare\Spider\Controller\Modules;
use Mediashare\Spider\Controller\Webspider;
use Mediashare\Crawler\Config as CrawlerConfig;


/**
 * WebSpider
 */
class Webspider
{
	public $url;
	public $config;
	public $modules = [];
	public $errors = [];
	public function __construct(Url $url, Config $config) {
		$this->url = $url;
		$this->config = $config;
		$this->output = new Output($config);
		$this->modules = new Modules($config);
		$config->setUrl($url);
	}

	public function run() {
		$this->output->banner();
		$config = new CrawlerConfig();
		$config->setVerbose($this->config->getVerbose());
		$crawler = new Crawler((string) $this->url, $config);
		$crawler->run();
		
		return $this;
	}
	
	public function modules(Url $url) {
		$modules = $this->modules->run($url);
		if (!empty($modules->errors)):
			foreach ($modules->errors as $index => $error) {
				$this->errors[] = $error;
			}
		endif;
		return $modules;
	}

	/**
	 * End Report
	 *
	 * @param Webspider $webpsider
	 * @return Result
	 */
	private function result(Webspider $webspider): Result {
		// Report
		$result = new Result($webspider);
		$result = $result->build();
		// Output
		$this->output->fileDirection($webspider->config->getOutput());
		$this->output->json($result->json());
		return $result;
	}
}