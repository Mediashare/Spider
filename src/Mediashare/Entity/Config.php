<?php

namespace Spider\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Spider\Entity\Url;

class Config
{
    private $id;
    private $urls = [];
    private $websites = [];
    private $webspider;
    private $search = [];
    private $pathRequire = [];
    private $pathException = [];
    public $json = false;
    public $output;

    public function __construct() {
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
     * @return Collection|Url[]
     */
    public function getUrls(): Collection
    {
        return $this->urls;
    }

    public function addUrl(Url $url): self
    {
        if (!empty($this->urls)):
            if (!$this->urls->contains($url)) {
                $this->urls[] = $url;
            }
        else
            $this->urls[] = $url;
        endif;

        return $this;
    }

    public function removeUrl(Url $url): self
    {
        if (!empty($this->urls)):
            if ($this->urls->contains($url)) {
                $this->urls->removeElement($url);
                // set the owning side to null (unless already changed)
                if ($url->getConfig() === $this) {
                    $url->setConfig(null);
                }
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
        foreach ($this->websites as $website) {
            if ($website->getDomain() === $url->getHost()) {
                return $website;
            }
        }
        return false;
    }

    /**
     * @return Collection|Website[]
     */
    public function getWebsites(string $host = null): Collection
    {
        return $this->websites;
    }

    public function addWebsite(Website $website): self
    {
        if (!empty($this->websites)):
            if (!$this->websites->contains($website)) {
            }
        else
            $this->websites[] = $website;
            $website->setConfig($this);
        endif;

        return $this;
    }

    public function removeWebsite(Website $website): self
    {
        if ($this->websites->contains($website)) {
            $this->websites->removeElement($website);
            // set the owning side to null (unless already changed)
            if ($website->getConfig() === $this) {
                $website->setConfig(null);
            }
        }

        return $this;
    }
}
