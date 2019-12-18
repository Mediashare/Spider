<?php

namespace Mediashare\Entity;

use Mediashare\Entity\Url;

class Config
{
    
    public $id; // Id|Name report
    public $url = "http://marquand.pro";
    public $websites = [];
    public $webspider = true; // Crawl all website
    public $requires = []; // Path required
    public $exceptions = []; // Path exceptions
    public $html = false; // Prompt html output
    public $json = false; // Prompt json output
    public $variables = []; // Variables Injected to modules
    public $modules = []; // Select one or more modules to use ##
    public $enable_modules = true; // Enable all modules
    public $modules_dir = __DIR__.'/Modules/'; // Default modules path ###
    public $reports_dir = __DIR__.'/../var/reports/'; // Default reports path ###
    public $output = null; // Rewrite ouput destination ###


    public function __construct()
    {
        $this->setId(uniqid());
    }

    public function getId(): ?string
    {
        if (!$this->id):
            $this->id = uniqid();
        endif;
        return $this->id;
    }

    public function setId(?string $id): self
    {
        if (!$id):$id = uniqid();endif;
        $this->id = $id;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $url = new Url($url);        
        $this->url = $url;

        $website = new Website($url);
        $this->addWebsite($website);
        
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

    public function getWebspider(): ?bool
    {
        return $this->webspider;
    }

    public function setWebspider(bool $webspider): self
    {
        $this->webspider = $webspider;

        return $this;
    }

    public function getRequires(): ?array
    {
        return $this->requires;
    }

    public function setRequires(?array $requires): self
    {
        $this->requires = $requires;

        return $this;
    }

    public function getExceptions(): ?array
    {
        return $this->exceptions;
    }

    public function setExceptions(?array $exceptions): self
    {
        $this->exceptions = $exceptions;

        return $this;
    }

    public function getHtml(): ?bool
    {
        return $this->html;
    }

    public function setHtml(bool $html): self
    {
        $this->html = $html;
        return $this;
    }

    public function getJson(): ?bool
    {
        return $this->json;
    }

    public function setJson(bool $json): self
    {
        $this->json = $json;
        return $this;
    }

    /**
     * @return array|Module[]
     */
    public function getModuless()
    {
        return $this->modules;
    }

    public function addModules(?array $modules): self
    {
        foreach ((array) $modules as $module) {
            if (!isset($this->modules[$module])):
                $this->modules[$module] = $module;
            endif;
        }

        return $this;
    }

    public function addModule(Module $module): self
    {
        if (!isset($this->modules[$module->name])):
            $this->modules[$module->name] = $url;
        endif;
     
        return $this;
    }

    public function removeModule(Module $module): self
    {
        if (isset($this->modules[$module->getModule()])):
            unset($this->modules[$module->getModule()]);
            // set the owning side to null (unless already changed)
            if ($module->getConfig() === $this) {
                $module->setConfig(null);
            }
        endif;

        return $this;
    }

    function addVariables($variables_injected) {
        foreach ($variables_injected as $module_name => $variables) {
            if (!is_array($variables)) { // Is Json Input
                $variables = json_decode($variables, true);
                foreach ((array) $variables as $module => $variable) {
                    $this->variables[$module][] = $variable;
                }
            } else {
                $this->variables[$module_name] = $variables;
            }
        }
    }

    public function enableAllModule(bool $enable): self
    {
        $this->enable_modules = $enable;
        return $this;
    }

    public function setReportsDir(string $reports_dir): self
    {
        $this->reports_dir = $reports_dir;
        return $this;
    }

    public function getReportsDir(): ?string
    {
        return $this->reports_dir;
    }

    public function setModulesDir(string $modules_dir): self
    {
        $this->modules_dir = $modules_dir;
        return $this;
    }

    public function getModulesDir(): ?string
    {
        return $this->modules_dir;
    }

    public function setOutput(?string $output): self
    {
        $this->output = $output;
        return $this;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }
}
