<?php

namespace Mediashare\Entity;

use Mediashare\Entity\Url;

class Config
{
    public $id; // Id|Name report
    public $url = "http://marquand.pro";
    public $webspider = true; // Crawl all website
    public $requires = []; // Path required
    public $exceptions = []; // Path exceptions
    public $verbose = false; // Prompt ouput (verbose & debug mode)
    public $json = false; // Prompt json output
    public $variables = []; // Variables Injected to modules
    public $modules = []; // Select one or more modules to use
    public $enable_modules = true; // Enable all modules

    public function __construct() {
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

    public function getUrl(): ?Url
    {
        return $this->url;
    }

    public function setUrl(?Url $url): self
    {    
        $this->url = $url;
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

    public function getVerbose(): ?bool
    {
        return $this->verbose;
    }

    public function setVerbose(bool $verbose): self
    {
        $this->verbose = $verbose;
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
    public function getModules()
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

    public function getReportsDir(): string
    {

        if (!$this->reports_dir):
            return __DIR__.'/../../var/reports/';
        endif;
        return $this->reports_dir;
    }

    public function setModulesDir(string $modules_dir): self
    {
        $this->modules_dir = $modules_dir;
        return $this;
    }

    public function getModulesDir(): string
    {
        if (!$this->modules_dir):
            return __DIR__.'/../Modules/';
        endif;
        return $this->modules_dir;
    }
}
