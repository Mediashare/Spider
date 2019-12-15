<?php
namespace App\Controller;

use Symfony\Component\DomCrawler\Crawler as Dom;
use App\Entity\Url;
use App\Entity\Website;
use App\Entity\WebPage;
use App\Controller\Module;

/**
 * Crawler
 */
class Crawler
{	
	public function crawl(WebPage $webPage) {
		$dom = new Dom($webPage->getBody()->getContent());
		$website = $webPage->getUrl()->getWebsite();
		$config = $website->getConfig();

		// Crawl links
		foreach($dom->filter('a') as $link) {
			if (!empty($link)) {
				$href = rtrim(ltrim($link->getAttribute('href')));
				if ($href) {
					$url = new Url($href);
					$isUrl = $url->checkUrl($webPage, $href);
					dump($url);die;
					if ($isUrl) { // newUrl Found
						if (!$config->getWebspider()) {$url->setExcluded(true);} // No crawling another pages
						else {$website->addUrl($url);}
					}
				}
			}
		}

		// Modules
		$this->modules($webPage);

		// Reset dom for memory
		$webPage->setBody(null);
   	}
	
	private function modules(WebPage $webPage) {
		$dom = new Dom($webPage->getBody()->getContent());
		$website = $webPage->getUrl()->getWebsite();
		$config = $website->getConfig();

		$module = new Module();
		$module->config = $config;
		$module->website = $website;
		$module->webpage = $webPage;
		$module->dom = $dom;
		// Get result
		$module->execute();
	}
}