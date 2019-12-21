<?php

namespace Mediashare\Entity;

use Mediashare\Entity\Website;
use Mediashare\Entity\Webpage;

class Url
{
    public $id;
    public $url;
    public $website;
    public $scheme;
    public $host;
    public $isCrawled;
    public $isExcluded;
    public $webpage;

    public function __toString() {
        return $this->getUrl();
    }

    public function __construct(string $url = "http://marquand.pro") {
        $this->setCrawled(false);
        $this->setExcluded(false);
        $this->setUrl($url);
        $website = new Website($this);
        $this->setWebsite($website);
        $webpage = new Webpage($this);
        $this->setWebpage($webpage);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        $this->setId($url);
        // Parse url
        $this->setScheme(parse_url($url, PHP_URL_SCHEME));
        $this->setHost(parse_url($url, PHP_URL_HOST));

        return $this;
    }

    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    public function setWebsite(?Website $website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    public function setScheme(?string $scheme): self
    {
        $this->scheme = $scheme;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function isCrawled(): ?bool
    {
        return $this->isCrawled;
    }

    public function setCrawled(bool $isCrawled): self
    {
        $this->isCrawled = $isCrawled;

        return $this;
    }

    public function isExcluded(): ?bool
    {
        return $this->isExcluded;
    }

    public function setExcluded(bool $isExcluded): self
    {
        $this->isExcluded = $isExcluded;

        return $this;
    }

    public function getWebpage(): ?Webpage
    {
        return $this->webpage;
    }

    public function setWebpage(?Webpage $webpage): self
    {
        $this->webpage = $webpage;

        return $this;
    }
    
    public function getSources(): ?array
    {
        $sources = [];
        $urls = (array) $this->website->getUrls();
        foreach ($urls as $url) {
            if ($url->isCrawled()) {
                $links = $url->getWebpage()->getLinks(); // Internal links
                foreach ($links as $link) {
                    if ($link == $this->getUrl() || 
                        rtrim(parse_url($link, PHP_URL_PATH), '/') === rtrim(parse_url($this->getUrl(), PHP_URL_PATH), '/')) {
                        $sources[$url->getUrl()] = $url->getUrl();
                    }
                }
            }
        }
        $sources = array_values($sources); // Reset array keys
        $this->sources = $sources;
        return $sources;
    }

    public function checkUrl(Config $config) {
        $url = $config->url;
        $website = $url->getWebsite();
        $url = $this->getUrl();
		if ($url == "/") {
            $url = rtrim($website->getScheme().'://'.$website->getDomain(),"/").$url;
        } elseif ($url[0] == "#") {
            $url = rtrim($url  ,"/")."/".$url;
        } else {
            $isUrl = filter_var($url, FILTER_VALIDATE_URL);
            if (!$isUrl && ($url[0] === "/" && $url[1] !== "/")) {
                $url = rtrim($website->getScheme().'://'.$website->getDomain(),"/").$url;
                $isUrl = filter_var($url, FILTER_VALIDATE_URL);
            }
			if (!$isUrl && strpos($url, $website->getScheme().'://'.$website->getDomain()) === false) {
                $url = rtrim($url,"/")."/".$url;
				$isUrl = filter_var($url, FILTER_VALIDATE_URL);
			}
            if (!$isUrl) {
                $url = rtrim($website->getScheme().'://'.$website->getDomain(),"/")."/".$url;
                $isUrl = filter_var($url, FILTER_VALIDATE_URL);
            }
        }

        $isUrl = filter_var($url, FILTER_VALIDATE_URL);

        // Exceptions
        $this->checkExceptions($url, $config);
        if ($isUrl) {return $url;} else {return false;}
    }
    
    public function checkExceptions(string $url, Config $config) {
        $this->__construct($url);
        $isUrl = filter_var($url, FILTER_VALIDATE_URL);
        if (!$isUrl) {
            $this->setExcluded(true);
        }
        $website = $config->url->getWebsite();
		if (\strpos($this->getUrl(), $website->getScheme().'://'.$website->getDomain()) === false) {
            $this->setExcluded(true);
        }        
		foreach ($config->getExceptions() as $value) {
			if (strpos($url, $value) !== false) {
				$this->setExcluded(true);
			}
		}
		foreach ($config->getRequires() as $value) {
			if (strpos($url, $value) === false) {
				$this->setExcluded(true);
			}
        }
        
        if (!$this->isExcluded()) {
            return true;
		} else {
            return false;
        }
    }
}
