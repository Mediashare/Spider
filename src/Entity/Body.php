<?php
namespace Spider\Entity;

class Body
{
    private $id;
    private $content;
    private $webPage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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
        if ($this !== $webPage->getBody()) {
            $webPage->setBody($this);
        }

        return $this;
    }
}
