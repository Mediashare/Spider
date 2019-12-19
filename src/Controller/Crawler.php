<?php
namespace Mediashare\Controller;

use Mediashare\Entity\Url;
use Mediashare\Entity\Config;
use Mediashare\Entity\WebPage;
use Mediashare\Entity\Website;
use Mediashare\Controller\Module;
use Symfony\Component\DomCrawler\Crawler as Dom;

/**
 * Crawler
 * 
 */
class Crawler
{	
	public $config;
	public $website;
	public $webpage;
	public function __construct(Config $config, Website $website, WebPage $webpage) {
		$this->config = $config;
		$this->website = $website;
		$this->webpage = $webpage;
	}
	public function crawl() {
		$dom = new Dom($this->webpage->getBody()->getContent());
		// Crawl links
		foreach($dom->filter('a') as $link) {
			if (!empty($link)) {
				$href = rtrim(ltrim($link->getAttribute('href')));
				if ($href) {
					$url = new Url($href);
					$isUrl = $url->checkUrl($this->webpage, $this->config);
					if ($isUrl) { // newUrl Found
						if (!$this->config->getWebspider()) {$url->setExcluded(true);} // No crawling another pages
						else {$this->website->addUrl($url);}
					}
				}
			}
		}

		// Modules
		$this->modules();

		// Reset dom for memory
		$this->webpage->setBody(null);
   	}
	
	private function modules() {
		$dom = new Dom($this->webpage->getBody()->getContent());
		$website = $this->webpage->getUrl()->getWebsite();

		$module = new Module();
		$module->config = $this->config;
		$module->website = $this->website;
		$module->webpage = $this->webpage;
		$module->dom = $dom;
		// Get result
		$module->execute();
	}
}