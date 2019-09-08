<?php
namespace App\Service;

use Symfony\Component\DomCrawler\Crawler as Dom;
use App\Webspider\Url;
use App\Webspider\Website;
use App\Webspider\WebPage;
use App\Service\Module;

/**
 * Crawler
 */
class Crawler
{
	public function __construct(WebPage $webPage) {
		$this->webPage = $webPage;
		$this->dom = new Dom($webPage->getBody()->getContent());
		$this->website = $webPage->getUrl()->getWebsite();
		$this->config = $this->website->getConfig();
	}

   public function crawl() {
		// Crawl links
		foreach($this->dom->filter('a') as $link) {
			if (!empty($link)) {
				$href = rtrim(ltrim($link->getAttribute('href')));
				if ($href) {
					$url = $this->checkUrl($href);
					if ($url) { // newUrl Found
						if (!$this->config->getWebspider()) {$url->setExcluded(true);} // No crawling another pages
						else {$this->website->addUrl($url);}
					}
				}
			}
		}

		// Modules
		$this->modules();

		// Reset dom for memory
		$this->webPage->setBody(null);
   	}
	
	private function modules() {
		$module = new Module();
		$module->config = $this->config;
		$module->website = $this->website;
		$module->webpage = $this->webPage;
		$module->dom = $this->dom;
		// Get result
		$module->execute();
	}
   

   
	private function checkUrl(string $url) {
		if ($url == "/" || $url[0] == "#") {
			$url = rtrim($this->website->getScheme().'://'.$this->website->getDomain(),"/").$url;
		} else {
         	$isUrl = filter_var($url, FILTER_VALIDATE_URL);
			if (!$isUrl && ($url[0] === "/" && $url[1] !== "/")) {
				$url = rtrim($this->website->getScheme().'://'.$this->website->getDomain(),"/").$url;
			} elseif (!$isUrl && strpos($url, $this->website->getScheme().'://'.$this->website->getDomain()) !== false) {
				$url = rtrim($this->website->getScheme().'://'.$this->website->getDomain(),"/")."/".$url;
			}
		}

		$url = new Url($url);
		// Exceptions
		if ($url->getHost() !== $this->website->getDomain()) {
			$url->setExcluded(true);
		}
		foreach ($this->config->getPathException() as $value) {
			if (strpos($url->getUrl(), $value) !== false) {
				$url->setExcluded(true);
			}
		}
		foreach ($this->config->getPathRequire() as $value) {
			if (strpos($url->getUrl(), $value) === false) {
				$url->setExcluded(true);
			}
		}

		if (!$url->isExcluded()) {
			$this->webPage->addLinks($url->getUrl());
			return $url;
		} else {
			$this->webPage->addExternalLinks($url->getUrl());
			return false;
		}
	}
}