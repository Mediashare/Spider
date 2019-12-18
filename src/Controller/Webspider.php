<?php
namespace Mediashare\Controller;

use Mediashare\Entity\Url;
use Mediashare\Entity\Config;
use Mediashare\Entity\Website;
use Mediashare\Controller\Guzzle;
use Mediashare\Service\Output;
use Mediashare\Controller\Report;
use Mediashare\Controller\Crawler;

/**
 * WebSpider
 */
class Webspider
{
	public $website;
	public $reports;
	public $counter = 0;
	public $config;
	public function __construct(Config $config) {
		$this->config = $config;
		$this->output = new Output($this->config);
		$this->guzzle = new Guzzle();
		$this->report = new Report($this->config);
		$this->report->output = $this->output;
	}

	public function run() {
		$url = $this->config->getUrl();
		$url = new Url($url);
		$website = new Website($url);
		$report = $this->crawl($website);
		return $report;
	}
	
	public function crawl(Website $website) {
		if (!$this->config->html) {$this->output->progressBar(0, 1);}
		while (count($website->getUrlsNotCrawled())) {
			foreach ($website->getUrlsNotCrawled() as $url) {
				// Check if have pathException & pathRequire
				if (strpos($url->getUrl(), $url->getWebsite()->getDomain()) === false) {$url->setExcluded(true);}
				if ((!$url->isExcluded() && !$url->isCrawled()) || $url === $website->getUrls()[0]) {
					$webPage = $this->guzzle->getWebPage($url);
					$this->output->progress($website, $webPage, $url);
					if ($webPage) {
						// Crawl
						$crawler = new Crawler();
						$crawler->crawl($this->config, $website, $webPage);
						$url->setCrawled(true);
						if (($this->counter % 100) === 0 || $this->counter === 1) {
							$this->report->create($website);
						}
					}
				}
			}
		}
		return $this->report->endResponse($website);
	}
}