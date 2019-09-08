<?php

namespace App\Webspider;

class Header
{
    private $id;
    private $httpCode;
    private $transferTime;
    private $downloadSize;
    private $webPage;
    private $content = [];

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

    public function getHttpCode(): ?int
    {
        return $this->httpCode;
    }

    public function setHttpCode(int $httpCode): self
    {
        $this->httpCode = $httpCode;

        return $this;
    }

    public function getTransferTime(): ?int
    {
        return $this->transferTime;
    }

    public function setTransferTime(?float $transferTime): self
    {
        $this->transferTime = (int) round($transferTime * 1000); // second to ms
        return $this;
    }

    public function getDownloadSize(): ?float
    {
        return $this->downloadSize;
    }

    public function setDownloadSize(?float $downloadSize): self
    {   
        $this->downloadSize = (int) round($downloadSize / 1000); // Bytes to Kb
        return $this;
    }

    public function getWebPage(): ?WebPage
    {
        return $this->webPage;
    }

    public function setWebPage(WebPage $webPage): self
    {
        $this->webPage = $webPage;

        // set the owning side of the relation if necessary
        if ($this !== $webPage->getHeader()) {
            $webPage->setHeader($this);
        }

        return $this;
    }

    public function getContent(): ?array
    {
        return $this->content;
    }

    public function setContent(?array $content): self
    {
        $this->content = $content;

        return $this;
    }
}
