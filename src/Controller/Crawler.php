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
	public function crawl(Config $config, Website $website, WebPage $webPage) {
		$dom = new Dom($webPage->getBody()->getContent());
		// Crawl links
		foreach($dom->filter('a') as $link) {
			if (!empty($link)) {
				$href = rtrim(ltrim($link->getAttribute('href')));
				if ($href) {
					$url = new Url($href);
					$isUrl = $url->checkUrl($webPage, $config);
					if ($isUrl) { // newUrl Found
						if (!$config->getWebspider()) {$url->setExcluded(true);} // No crawling another pages
						else {$website->addUrl($url);}
					}
				}
			}
		}

		// Modules
		$this->modules($config, $webPage);

		// Reset dom for memory
		$webPage->setBody(null);
   	}
	
	private function modules(Config $config, WebPage $webPage) {
		$dom = new Dom($webPage->getBody()->getContent());
		$website = $webPage->getUrl()->getWebsite();

		$module = new Module();
		$module->config = $config;
		$module->website = $website;
		$module->webpage = $webPage;
		$module->dom = $dom;
		// Get result
		$module->execute();
	}
}