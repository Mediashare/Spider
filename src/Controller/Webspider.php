<?php
namespace Mediashare\Spider\Controller;

use Mediashare\Spider\Entity\Url;
use Mediashare\Spider\Entity\Config;
use Mediashare\Spider\Entity\Module;
use Mediashare\Spider\Entity\Result;
use Mediashare\Spider\Entity\Website;
use Mediashare\Spider\Service\Output;
use Mediashare\Spider\Controller\Report;
use Mediashare\Spider\Controller\Crawler;
use Mediashare\Spider\Controller\Modules;


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
		$config->setUrl($url);
		$this->output = new Output($this->config);
		$this->modules = new Modules($this->config);
	}

	public function run() {
        $this->output->banner();
		$report = $this->loop($this->url->getWebsite());
		return $report;
	}
	
	public function loop(Website $website) {
		$counter = 0;
		while (count($website->getUrlsNotCrawled())) {
			$urls = $website->getUrlsNotCrawled();
			foreach ($urls as $url) {
				$crawler = $this->crawl($url);
				if ($crawler) {
					// Execute Module(s)
					$this->modules = $this->modules($url);
					// Report
					if (($counter % 100) === 0 || $counter === 1) {
						$report = new Result($this);
						$report = $report->build();
					}
				}
			}
		}
		// Report
		$report = new Result($this);
		$report = $report->build();
		// Output
		$this->output->fileDirection($this->config->getOutput());
		$this->output->json($report->json());
		return $report;
	}

	public function crawl(Url $url) {
		// Check if have pathException & pathRequire
		if ((!$url->isExcluded() && !$url->isCrawled()) || $url === $url->getWebsite()->getUrls()[0]) {
			// Progress
			$this->progress($url);
			// Crawl
			$crawler = new Crawler($url, $this->config);
			$crawler->run();
			$url->setWebpage($crawler->webpage);
			return $crawler;
		} else {
			return false;
		}
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

	private function progress(Url $url) {
		$webpage = $url->getWebpage();
		$website = $this->url->getWebsite();
		$counter = count($website->getUrlsCrawled()) + 1;
		$max_counter = (count($website->getUrlsCrawled()) + count($website->getUrlsNotCrawled()));
		if ($webpage->getHeader()) {$requestTime = $webpage->getHeader()->getTransferTime()."ms";} else {$requestTime = null;}
		$message = $this->output->echoColor("--- (".$counter."/".$max_counter.") URL: [".$url->getUrl()."] ".$requestTime." ---", "white");
		$this->output->progressBar($counter, $max_counter, $message);
		
	}
}