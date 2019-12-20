<?php

namespace Mediashare\Entity;

use Mediashare\Entity\Config;
use Mediashare\Entity\Website;
use Mediashare\Controller\Modules;
use Mediashare\Controller\Webspider;
use Mediashare\Entity\Module as ModuleEntity;

class Result
{
    public $id;
    public $config;
    public $website;
    public $urls = [];
    public $modules = [];
    public $errors = [];
    public function __construct(Webspider $webspider) {
        $this->webspider = $webspider;
        $this->id = uniqid();
        $this->config = $webspider->config;
        $this->modules = $webspider->modules;
        $this->website = $webspider->url->getWebsite();
        $this->errors = $webspider->errors;
    }
    
    public function build() {
        $this->setModules($this->modules);
        $this->setUrls($this->website);
        return $this;
    }

    public function setModules(Modules $modules) {
        $this->modules = $modules->results;
        return $this;
    }

    public function setUrls(Website $website) {
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
