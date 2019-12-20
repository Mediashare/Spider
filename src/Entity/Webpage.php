<?php

namespace Mediashare\Entity;

use Mediashare\Entity\Header;
use Mediashare\Entity\Body;
use Mediashare\Entity\Url;

class Webpage
{
    public $id;
    public $url;
    public $header;
    public $body;
    public $links = [];
    public $externalLinks = [];

    public function __construct(Url $url) {
        $this->setId(uniqid());
        $this->setUrl($url);
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

    public function __toString() {
        return $this->getUrl()->getUrl();
    }

    public function getHeader(): ?Header
    {
        return $this->header;
    }

    public function setHeader(?Header $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getBody(): ?Body
    {
        return $this->body;
    }

    public function setBody(?Body $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getUrl(): ?Url
    {
        return $this->url;
    }

    public function setUrl(?Url $url): self
    {
        $this->url = $url;
        return $this;
    }
    
    public function addLinks(string $url): self
    {
        if (!in_array($url, $this->links)) {
            $this->links[] = $url;
        }
        return $this;
    }

    public function getLinks(): ?array
    {
        return $this->links;
    }

    public function setLinks(?array $links): self
    {
        $this->links = $links;
        return $this;
    }

    public function addExternalLinks(string $url): self
    {
        if (!in_array($url, $this->externalLinks)) {
            $this->externalLinks[] = $url;
        }
        
        return $this;
    }

    public function getExternalLinks(): ?array
    {
        return $this->externalLinks;
    }
    
    public function setExternalLinks(?array $externalLinks): self
    {
        $this->externalLinks = $externalLinks;
        return $this;
    }
}
