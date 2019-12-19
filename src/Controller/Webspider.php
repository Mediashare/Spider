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
	public $url;
	public $config;
	public $website;
	public $reports;
	public $counter = 0;
	public function __construct(Url $url, Config $config) {
		$this->url = $url;
		$this->config = $config;
		$this->output = new Output($this->config);
		$this->report = new Report($this->config, $this->output);
		$this->guzzle = new Guzzle();
	}

	public function run() {
		$website = new Website($this->url);
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
					$guzzle = $this->guzzle->getWebPage($url);
					$this->output->progress($website, $guzzle, $url);
					if ($guzzle) {
						// Crawl
						$crawler = new Crawler($this->config, $website, $guzzle);
						$crawler->crawl();
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