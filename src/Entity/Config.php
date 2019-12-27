<?php

namespace Mediashare\Spider\Entity;

use Mediashare\Spider\Entity\Url;

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
    public $enableModules = true; // Enable all modules
    public $modulesDir;
    public $reportsDir;
    public $output;
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

    public function getUrl()
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
        $this->enableModules = $enable;
        return $this;
    }

    public function setReportsDir(string $reportsDir): self
    {
        $this->reportsDir = $reportsDir;
        return $this;
    }

    public function getReportsDir(): string
    {

        if (!$this->reportsDir):
            $this->setReportsDir(__DIR__.'/../../reports/');
        endif;
        return $this->reportsDir;
    }

    public function setModulesDir(string $modulesDir): self
    {
        $this->modulesDir = $modulesDir;
        return $this;
    }

    public function getModulesDir(): string
    {
        if (!$this->modulesDir):
            $this->setModulesDir(__DIR__.'/../../modules/');
        endif;
        return $this->modulesDir;
    }

    public function setOutput(string $output): self
    {
        $this->output = $output;
        return $this;
    }

    public function getOutput(): string
    {
        if (!$this->output):
            $domain = $this->getUrl()->getHost();
            $this->setOutput($this->getReportsDir().$domain.'/'.$this->getId().'.json');
        endif;
        return $this->output;
    }
}
