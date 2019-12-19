<?php

namespace Mediashare\Entity;

use Mediashare\Entity\Config;
use Mediashare\Entity\Website;

class Result
{
    public $id;
    public $config;
    public $website;
    public $urls;
    public $modules;
    public $errors;

    public function __construct(config $config, Website $website) {
        $this->id = uniqid();
        $this->config = $config;
        $this->website = $website;
    }
    
    public function build() {
        $this->setUrls($this->website);

        // Modules
        if (isset($this->website->modules)) {$this->modules = $this->website->modules;}
        // Errors
        if (isset($this->website->errors)) {$this->errors = $this->website->errors;}
        return $this;
    }

    public function setUrls(?Website $website) {
        if (!$website) {$website = $this->website;}
        // Urls
        foreach ((array)$website->getUrls() as $url) {
            // Add Url
            $this->urls[$url->getUrl()] = [
                'url' => $url->getUrl(),
                'isCrawled' => $url->isCrawled(),
                'isExcluded' => $url->isExcluded(),
                'sources' => $url->getSources($website)
            ];

            $webpage = $url->getWebPage();
            if ($webpage) {
                // Url Header
                $header = $url->getWebPage()->getHeader();
                $headers = [
                    'httpCode' => $header->getHttpCode(),
                    'transferTime' => $header->getTransferTime(),
                    'downloadSize' => $header->getDownloadSize(),
                    'headers' => $header->getContent()
                ];
                $this->urls[$url->getUrl()]['header'] = $headers;
                // Url links
                $links = [
                    'links' => $webpage->getLinks(),
                    'externalLinks' => $webpage->getExternalLinks(),
                ];
                $this->urls[$url->getUrl()]['links'] = $links;
                // Modules
                if (isset($webpage->modules)) {
                    $this->urls[$url->getUrl()]['modules'] = $webpage->modules;
                }
            }
        }
    }
}
