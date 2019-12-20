<?php
namespace Mediashare\Controller;

use Mediashare\Entity\Url;
use Mediashare\Entity\Body;
use Mediashare\Entity\Config;
use Mediashare\Entity\WebPage;
use Mediashare\Entity\Website;
use Mediashare\Controller\Guzzle;
use Mediashare\Controller\Module;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Crawler
 * 
 */
class Crawler
{	
	public $url;
	public $config;
	public $guzzle;
	public $crawler;
	public $webpage;
	public function __construct(Url $url, Config $config) {
		$this->url = $url;
		$this->config = $config;
	}

	public function run() {
		$this->crawler = $this->getDomCrawler($this->url);
		// Crawl links
		foreach($this->crawler->filter('a') as $link) {
			if (!empty($link)) {
				$href = rtrim(ltrim($link->getAttribute('href')));
				if ($href) {
					$url = new Url($href);
					$isUrl = $url->checkUrl($this->config);
					if ($isUrl) {
						if (!$url->isExcluded()) { // newUrl Found
							if (!$this->config->getWebspider()) {$url->setExcluded(true);} // No crawling another pages
							else {
								$this->url->getWebsite()->addUrl($url);
								$this->url->getWebpage()->addLinks($url->getUrl());
							}
						} else {
							$this->url->getWebpage()->addExternalLinks($url->getUrl());
						}
					}
				}
			}
		}
		$this->url->setCrawled(true);
		return $this;
	}

	public function getDomCrawler(Url $url) {
		// Guzzle get Webpage content
		$guzzle = new Guzzle($url);
		$guzzle = $guzzle->run();
		$body = $guzzle->body;
		$this->webpage = $guzzle->webpage;
		// Generate DomCrawler (Symfony Library)
		$crawler = new DomCrawler($body->getContent());
		return $crawler;
	}
}