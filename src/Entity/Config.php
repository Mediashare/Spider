<?php

namespace Spider\Entity;

use Spider\Entity\Url;

class Config
{
    private $id;
    private $urls;
    private $webspider;
    private $search = [];
    private $pathRequire = [];
    private $pathException = [];
    private $websites;
    public $json = false;
    public $output;

    public function __construct()
    {
        $this->setId(uniqid());
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

    /**
     * @return array|Url[]
     */
    public function getUrls()
    {
        return $this->urls;
    }

    public function addUrl(Url $url): self
    {
        if (!isset($this->urls[$url->getUrl()])):
            $this->urls[$url->getUrl()] = $url;
        endif;

        return $this;
    }

    public function removeUrl(Url $url): self
    {
        if (isset($this->urls[$url->getUrl()])):
            unset($this->urls[$url->getUrl()]);
            // set the owning side to null (unless already changed)
            if ($url->getConfig() === $this) {
                $url->setConfig(null);
            }
        endif;

        return $this;
    }

    public function getWebspider(): ?bool
    {
        return $this->webspider;
    }

    public function setWebspider(bool $webspider): self
    {
        $this->webspider = $webspider;

        return $this;
    }

    public function getSearch(): ?array
    {
        return $this->search;
    }

    public function setSearch(?array $search): self
    {
        $this->search = $search;

        return $this;
    }

    public function getPathRequire(): ?array
    {
        return $this->pathRequire;
    }

    public function setPathRequire(?array $pathRequire): self
    {
        $this->pathRequire = $pathRequire;

        return $this;
    }

    public function getPathException(): ?array
    {
        return $this->pathException;
    }

    public function setPathException(?array $pathException): self
    {
        $this->pathException = $pathException;

        return $this;
    }

    public function getWebsite(Url $url)
    {
        foreach ((array) $this->websites as $website) {
            if ($website->getDomain() === $url->getHost()) {
                return $website;
            }
        }
        return false;
    }

    /**
     * @return array|Website[]
     */
    public function getWebsites(string $host = null)
    {
        return $this->websites;
    }

    public function addWebsite(Website $website): self
    {
        if (!isset($this->websites[$website->getDomain()])):
            $this->websites[$website->getDomain()] = $website;
            $website->setConfig($this);
        endif;

        return $this;
    }

    public function removeWebsite(Website $website): self
    {
        if (isset($this->websites[$website->getDomain()])):
            unset($this->websites[$website->getDomain()]);
            // set the owning side to null (unless already changed)
            if ($website->getConfig() === $this) {
                $website->setConfig(null);
            }
        endif;

        return $this;
    }
}
