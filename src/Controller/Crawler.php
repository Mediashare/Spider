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
					$url = $this->checkUrl($webPage, $href);
					if ($url) { // newUrl Found
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
   

   
	private function checkUrl(WebPage $webPage, string $url) {
		$website = $webPage->getUrl()->getWebsite();
		$config = $website->getConfig();

		if ($url == "/" || $url[0] == "#") {
			$url = rtrim($website->getScheme().'://'.$website->getDomain(),"/").$url;
		} else {
         	$isUrl = filter_var($url, FILTER_VALIDATE_URL);
			if (!$isUrl && ($url[0] === "/" && $url[1] !== "/")) {
				$url = rtrim($website->getScheme().'://'.$website->getDomain(),"/").$url;
			} elseif (!$isUrl && strpos($url, $website->getScheme().'://'.$website->getDomain()) !== false) {
				$url = rtrim($website->getScheme().'://'.$website->getDomain(),"/")."/".$url;
			}
		}

		$url = new Url($url);
		// Exceptions
		if ($url->getHost() !== $website->getDomain()) {
			$url->setExcluded(true);
		}
		foreach ($config->getPathException() as $value) {
			if (strpos($url->getUrl(), $value) !== false) {
				$url->setExcluded(true);
			}
		}
		foreach ($config->getPathRequire() as $value) {
			if (strpos($url->getUrl(), $value) === false) {
				$url->setExcluded(true);
			}
		}

		if (!$url->isExcluded()) {
			$webPage->addLinks($url->getUrl());
			return $url;
		} else {
			$webPage->addExternalLinks($url->getUrl());
			return false;
		}
	}
}