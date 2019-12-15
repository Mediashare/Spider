<?php
namespace Spider\Controller;

use Spider\Entity\Config;
use Spider\Entity\Url;
use Spider\Controller\Output;
use Spider\Controller\Guzzle;
use Spider\Controller\Crawler;
use Spider\Controller\Report;

/**
 * WebSpider
 */
class Webspider
{
	public function __construct(Config $config) {
		$this->config = $config;
		$this->guzzle = new Guzzle();
		$this->report = new Report();
		$this->output = new Output();
	}

	public function run() {
		$websites = $this->config->getWebsites();
		foreach ($websites as $website) {
			$counter = 0;
			if (!$this->config->html) {$this->output->progressBar($counter, count($website->getUrlsNotCrawled()));}
			while (count($website->getUrlsNotCrawled())) {
				foreach ($website->getUrlsNotCrawled() as $url) {
					// Check if have pathException & pathRequire
					if (strpos($url->getUrl(), $url->getWebsite()->getDomain()) === false) {$url->setExcluded(true);}
					if ((!$url->isExcluded() && !$url->isCrawled()) || $url === $website->getUrls()[0]) {
						$webPage = $this->guzzle->getWebPage($url);
						if ($webPage) {
							// Crawl
							$crawler = new Crawler();
							$crawler->crawl($webPage);
							$url->setCrawled(true);
							// ProgressBar
							$counter++;
							if ($webPage) {$requestTime = $webPage->getHeader()->getTransferTime()."ms";} else {$requestTime = null;}
							if ($this->config->html) {
								$message = "--- (".$counter.") URL: [".$url->getUrl()."] ".$requestTime." --- <br/> \n";
								echo $message;
							} else {
								$message = $this->output->echoColor("--- (".$counter.") URL: [".$url->getUrl()."] ".$requestTime." ---", 'cyan');
								$this->output->progressBar($counter, count($website->getUrls()), $message);
							}
							if (($counter % 100) === 0 || $counter === 1) {
								$this->report->create($website);
							}
						}
					}
				}
			}
			$this->report->endResponse($website);
		}
	}
}