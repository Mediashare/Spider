<?php
namespace App\Service;

use App\Webspider\Config;
use App\Webspider\Url;
use App\Service\Output;
use App\Service\Guzzle;
use App\Service\Crawler;
use App\Service\Report;

/**
 * WebSpider
 */
class Webspider
{
	public function __construct(Output $output) {
		$this->output = $output;
		$this->guzzle = new Guzzle();
		$this->report = new Report();
	}

	public function run(Config $config) {
		$websites = $config->getWebsites();
		foreach ($websites as $website) {
			$counter = 0;
			$this->output->progressBar($counter, count($website->getUrlsNotCrawled()));
			while (count($website->getUrlsNotCrawled())) {
				foreach ($website->getUrlsNotCrawled() as $url) {
					// Check if have pathException & pathRequire
					if (strpos($url->getUrl(), $url->getWebsite()->getDomain()) === false) {
						$url->setExcluded(true);
					}
					if ((!$url->isExcluded() && !$url->isCrawled()) || $url === $website->getUrls()[0]) {
						$webPage = $this->guzzle->getWebPage($url);
						if ($webPage) {
							// Crawl
							$crawler = new Crawler($webPage);
							$crawler->crawl();
							$url->setCrawled(true);
							// ProgressBar
							$counter++;
							if ($webPage) {$requestTime = $webPage->getHeader()->getTransferTime()."ms";} else {$requestTime = null;}
							$message = $this->output->echoColor("--- (".$counter.") URL: [".$url->getUrl()."] ".$requestTime." ---", 'cyan');
							$this->output->progressBar($counter, count($website->getUrls()), $message);
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