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
	public function __construct() {
		$this->output = new Output();
		$this->guzzle = new Guzzle();
		$this->report = new Report();
	}

	public function run(Config $config) {
		// var_dump($config);die;
		$websites = $config->getWebsites();
		foreach ($websites as $website) {
			$counter = 0;
			if (!$config->html) {$this->output->progressBar($counter, count($website->getUrlsNotCrawled()));}
			while (count($website->getUrlsNotCrawled())) {
				foreach ($website->getUrlsNotCrawled() as $url) {
					// Check if have pathException & pathRequire
					if (strpos($url->getUrl(), $url->getWebsite()->getDomain()) === false) {$url->setExcluded(true);}
					if ((!$url->isExcluded() && !$url->isCrawled()) || $url === $website->getUrls()[0]) {
						$webPage = $this->guzzle->getWebPage($url);
						// ProgressBar
						$counter++;
						if ($webPage) {$requestTime = $webPage->getHeader()->getTransferTime()."ms";} else {$requestTime = null;}
						if ($config->html) {
							$message = $this->output->echoColor("--- (".$counter.") URL: [".$url->getUrl()."] ".$requestTime." --- \n", 'cyan');
							echo $message;
						} elseif (!$config->json) {
							$message = $this->output->echoColor("--- (".$counter.") URL: [".$url->getUrl()."] ".$requestTime." ---", 'cyan');
							$this->output->progressBar($counter, count($website->getUrls()), $message);
						}

						if ($webPage) {
							// Crawl
							$crawler = new Crawler();
							$crawler->crawl($webPage);
							$url->setCrawled(true);
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