<?php
namespace Mediashare\Spider\Controller;

use Mediashare\Crawler\Crawler;
use Mediashare\Spider\Entity\Url;
use Mediashare\Spider\Entity\Config;
use Mediashare\Spider\Entity\Result;
use Mediashare\Spider\Service\Output;
use Mediashare\Spider\Service\Modules;
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
		$modules = new Modules($this->crawler, $this->config);
		$this->modules = $modules->run();
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
		$config->setWebspider($this->config->getWebspider());
		$config->setVerbose($this->config->getVerbose());
		$crawler = new Crawler((string) $this->url, $config);
		$crawler->run();
		return $crawler;
	}
}