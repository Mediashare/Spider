<?php

namespace Mediashare\Entity;

use Mediashare\Entity\Url;

class Website
{
    public $id;
    public $domain;
    public $urls;

    public function __toString() {
        return $this->getDomain();
    }

    public function __construct(Url $url) {
        $this->url = $url;
        $this->setDomain((string) $url->getHost());
        $this->setScheme((string) $url->getScheme());
        $this->addUrl($url);
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

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;
        $this->setId($domain);
        return $this;
    }

    public function getUrlsCrawled() {
        $urls = [];
        foreach ($this->getUrls() as $url) {
            if ($url->isCrawled()) {
                $urls[(string) $url] = $url;
            }
        }
        return $urls;
    }

    public function getUrlsNotCrawled() {
        $urls = [];
        foreach ($this->getUrls() as $url) {
            if (!$url->isCrawled() && !$url->isExcluded()) {
                $urls[(string) $url] = $url;
            }
        }
        return $urls;
    }

    /**
     * @return array|Url[]
     */
    public function getUrls()
    {
        return $this->urls;
    }

    public function addUrl(Url $newUrl): self
    {
        $excluded = false;
        foreach ((array) $this->urls as $url) {
            // If already saved
            if ($url->getUrl() === $newUrl->getUrl()) {
                $excluded = true;
            }
        }

        if (!$excluded) {
            $this->urls[$newUrl->getUrl()] = $newUrl;
            $newUrl->setWebsite($this);
        }

        return $this;
    }

    public function removeUrl(Url $url): self
    {
        if (isset($this->urls[$url->getUrl()])):
            unset($this->urls[$url->getUrl()]);
        endif;
        return $this;
    }

    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    public function setScheme(string $scheme): self
    {
        $this->scheme = $scheme;

        return $this;
    }
}
