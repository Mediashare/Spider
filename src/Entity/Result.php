<?php

namespace Mediashare\Entity;

use Mediashare\Entity\Config;
use Mediashare\Entity\Website;
use Mediashare\Controller\Modules;
use Mediashare\Controller\Webspider;
use Mediashare\Entity\Module;

class Result
{
    public $id;
    public $config;
    public $website;
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
    
    public function build(): self {
        $this->setModules($this->modules);
        $this->setUrls($this->website);
        return $this;
    }

    public function setConfig(Config $config) {
        $this->config = $config;
        return $this;
    }

    public function setModules(Modules $modules): self {
        $this->modules = $modules->results;
        return $this;
    }

    public function setUrls(Website $website): self {
        // Urls
        foreach ((array)$website->getUrls() as $url) {
            // $url->getWebpage()->setBody(null);
            // $url->getSources();
            // dump($sources);
            // $this->urls[(string) $url->getUrl()] = $url;
        }
        return $this;
    }
}
