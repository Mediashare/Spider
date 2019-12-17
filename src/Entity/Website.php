<?php

namespace Spider\Entity;

use Spider\Entity\Url;

class Website
{
    private $id;
    private $domain;
    private $urls;
    private $createDate;
    private $updateDate;
    private $config;
    public $errors = [];

    public function __toString() {
        return $this->getDomain();
    }

    public function __construct(Url $url) {
        $this->setUpdateDate();
        $this->setDomain($url->getHost());
        $this->setScheme($url->getScheme());
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
                $urls[] = $url;
            }
        }
        return $urls;
    }

    public function getUrlsNotCrawled() {
        $urls = [];
        foreach ($this->getUrls() as $url) {
            if (!$url->isCrawled() && !$url->isExcluded()) {
                $urls[] = $url;
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
            if ($url->getUrl() === $newUrl->getUrl() ||
                $url->getPath() === $newUrl->getPath()) {
                $excluded = true;
            }
        }

        if (!$excluded) {
            $this->urls[] = $newUrl;
            $newUrl->setWebsite($this);
        }

        return $this;
    }

    public function removeUrl(Url $url): self
    {
        if ($this->urls->contains($url)) {
            $this->urls->removeElement($url);
            // set the owning side to null (unless already changed)
            if ($url->getWebsite() === $this) {
                $url->setWebsite(null);
            }
        }

        return $this;
    }

    public function getConfig(): ?Config
    {
        return $this->config;
    }

    public function setConfig(?Config $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function getCreateDate(): ?\DateTime
    {
        return $this->createDate;
    }

    public function setCreateDate(): self
    {
        $this->createDate = new \DateTime();

        return $this;
    }

    public function getUpdateDate(): ?\DateTime
    {
        return $this->updateDate;
    }

    public function setUpdateDate(): self
    {
        if (!$this->createDate) {
            $this->setCreateDate();
        }
        $this->updateDate = new \DateTime();

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
