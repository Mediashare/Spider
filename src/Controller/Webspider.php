<?php
namespace Mediashare\Controller;

use Mediashare\Entity\Url;
use Mediashare\Entity\Config;
use Mediashare\Entity\Module;
use Mediashare\Entity\Website;
use Mediashare\Service\Output;
use Mediashare\Controller\Report;
use Mediashare\Controller\Crawler;
use Mediashare\Controller\Modules;


/**
 * WebSpider
 */
class Webspider
{
	public $url;
	public $config;
	public $modules = [];
	public $errors = [];
	public $counter = 0;
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
		while (count($website->getUrlsNotCrawled())) {
			$urls = $website->getUrlsNotCrawled();
			foreach ($urls as $url) {
				$crawler = $this->crawl($url);
				if ($crawler) {
					// Execute Module(s)
					$this->modules = $this->modules($url);
					// Report
					if (($this->counter % 100) === 0 || $this->counter === 1) {
						$this->report();
					}
				}
			}
		}
		$report = $this->report($end = true);
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

	
	/**
	 * Build Report & Create Json File
	 *
	 * @param boolean $end if true then output file direction.
	 * @return Report
	 */
	public function report(bool $end = false) {
		// Report
		$report = new Report($this);
		$report->build();
		$report = $report->create($end);
		return $report;
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