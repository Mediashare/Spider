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
	public function __construct(Url $url, Config $config) {
		$this->url = $url;
		$this->config = $config;
	}

	public function run() {
		$this->crawler = $this->getCrawler($this->url);
		// Crawl links
		foreach($this->crawler->filter('a') as $link) {
			if (!empty($link)) {
				$href = rtrim(ltrim($link->getAttribute('href')));
				if ($href) {
					$url = new Url($href);
					$isUrl = $url->checkUrl($this->guzzle->webpage, $this->config);
					if ($isUrl) { // newUrl Found
						if (!$this->config->getWebspider()) {$url->setExcluded(true);} // No crawling another pages
						else {$this->url->getWebsite()->addUrl($url);}
					}
				}
			}
		}
		$this->url->setCrawled(true);
		return $this;
	}

	public function getCrawler() {
		// Guzzle get Webpage content
		$this->guzzle = new Guzzle($this->url);
		$this->guzzle = $this->guzzle->run();
		$body = $this->guzzle->body;
		// Generate DomCrawler (Symfony Library)
		$crawler = new DomCrawler($body->getContent());
		return $crawler;
	}
}