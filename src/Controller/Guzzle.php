<?php
namespace Mediashare\Controller;

use GuzzleHttp\TransferStats;
use GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;
use Mediashare\Entity\Url;
use Mediashare\Entity\WebPage;
use Mediashare\Entity\Header;
use Mediashare\Entity\Body;

class Guzzle
{
	public function __construct() {
		$this->guzzle = new Client([
			'http_errors' => false, 
		]);
   	}
   
   public function getWebPage(Url $url) {
		$webPage = new WebPage();
		$this->header = new Header();
		$body = new Body();
		$webPage->setUrl($url);
		$this->header->setWebPage($webPage);
		$body->setWebPage($webPage);
		$website = $url->getWebsite();

		try {
			$guzzle = $this->guzzle->request('GET', $url->getUrl(), [
				'headers' => [
					'User-Agent' => $this->getUserAgent()
				],
				'verify' => false,
				'on_stats' => function (TransferStats $stats) {
					$performances = $stats->getHandlerStats();
					// $this->header->setTransferTime($stats->getTransferTime());
					// You must check if a response was received before using the
					// response object.
					if ($stats->hasResponse()) {
						// If php-curl is not installed
						if (isset($performances["size_download"])) {$this->header->setDownloadSize($performances["size_download"]);}
						if (isset($performances["total_time"])) {$this->header->setTransferTime($performances["total_time"]);}
					}
				}
			]);	
		} catch (RequestException $exception) {
			$url->setExcluded(true);
			$website->errors[] = [
				'type' => 'guzzle',
				'message' => $exception->getMessage(),
				'url' => $url->getUrl(),
			];
			return false;
		}
		$httpCode = $guzzle->getStatusCode();
		// Error httpCode
		if ($httpCode >= 400 ) {
			$this->header->setHttpCode($httpCode);
			foreach ($guzzle->getHeaders() as $name => $values) {
				$headers[$name] = implode(', ', $values);
			}
			$url->setExcluded(true);
			$website->errors[] = [
				'type' => 'guzzle',
				'message' => 'Response status: '.$httpCode,
				'url' => $url->getUrl(),
			];
			return false;
		}
		
		$this->header->setHttpCode($httpCode);
		foreach ($guzzle->getHeaders() as $name => $values) {
			$headers[$name] = implode(', ', $values);
		}
		$this->header->setContent($headers);
		$body->setContent($guzzle->getBody());

		return $webPage;
	}
	private function getUserAgent() {
		$userAgents = [
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.47 Safari/537.36',
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36',
			'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36',
			'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36',
			'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.91 Safari/537.36',
			'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:55.0) Gecko/20100101 Firefox/55.0',
			'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36',
			'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.110 Safari/537.36'
		];
		return $userAgents[rand(0, (count($userAgents) - 1))];
	}
}
