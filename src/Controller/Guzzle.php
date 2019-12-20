<?php
namespace Mediashare\Controller;

use GuzzleHttp\TransferStats;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Mediashare\Entity\Url;
use Mediashare\Entity\Webpage;
use Mediashare\Entity\Header;
use Mediashare\Entity\Body;

class Guzzle
{
	public $url;
	public $webpage;
	public $header;
	public $body;
	public $errors = [];
	public function __construct(Url $url) {
		$this->url = $url;
		$this->guzzle = new Client([
			'http_errors' => false, 
		]);
   	}
   
   public function run() {
		$this->webpage = new Webpage($this->url);
		$this->header = new Header();
		$this->body = new Body();
		try {
			$guzzle = $this->guzzle->request('GET', $this->url->getUrl(), [
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
			$this->url->setExcluded(true);
			$this->url->isCrawled(false);
			$this->errors[] = [
				'type' => 'guzzle',
				'message' => $exception->getMessage(),
				'url' => $this->url->getUrl(),
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
			$this->url->setExcluded(true);
			$this->errors[] = [
				'type' => 'guzzle',
				'message' => 'Response status: '.$httpCode,
				'url' => $this->url->getUrl(),
			];
			return false;
		}
		
		$this->header->setHttpCode($httpCode);
		foreach ($guzzle->getHeaders() as $name => $values) {
			$headers[$name] = implode(', ', $values);
		}
		$this->header->setContent($headers);
		$this->body->setContent($guzzle->getBody());

		$this->webpage->setHeader($this->header);
		$this->webpage->setBody($this->body);

		return $this;
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
