<?php
namespace App\Controller;
use App\Controller\FileSystem;

class Module {
    
    public $config;
    public $website;
    public $webpage;
    public $dom;

    public function execute() {
		// Modules
        $modules = $this->getModules();
        $results = [];
        foreach ($modules as $module) {
            // Set required Object in Module
            $module->config = $this->config;
			$module->webpage = $this->webpage;
            $module->dom = $this->dom;
            $name = $module->name;
            $description = $module->description;
            $variables = $module->variables;
            if ($variables) {
                $module->variables = [];
                if (isset($this->config->variables[$module->className])) {
                    $module->variables = (array) $this->getVariables($this->config->variables[$module->className]);
                }
            }

            // Execute this Module
            $result = $module->run();
            
            // Report
            $this->website->modules[$name]['name'] = $name;
            $this->website->modules[$name]['description'] = $description;
            if ($result) {
                $this->website->modules[$name]['results'][(string) $this->webpage->getUrl()] = $result;
            }
            if ($module->errors) {
                $this->website->errors = array_merge($this->website->errors, $module->errors);
            }
        }
    }
    
    public function getModules() {
        $moduleDir = __DIR__.'/../../'.$_ENV['MODULES_DIR'];
        $modulesFiles = glob($moduleDir.'*.php');
        $modules = [];
        foreach($modulesFiles as $moduleFile) {
            $className = "App\\Modules\\".basename($moduleFile, '.php');
            $module = new $className();
            $module->className = basename($moduleFile, '.php');
            if ($this->config->modules === true || in_array(basename($moduleFile, '.php'), $this->config->modules)) {
                $modules[] = $module;
            }
        }
        return $modules;
    }


    public function getVariables(array $tabVariables) {
        foreach ($tabVariables as $key => $listVariables) {
            foreach ($listVariables as $key => $variable) {
                if (is_array($variable)) {
                    foreach ($variable as $value) {
                        if (is_array($value)) {
                            $values = $value;
                            foreach ($values as $value) {
                                $variables[$key] = $value;
                            }
                        } else {
                            $variables[$key] = $value;
                        }
                    }
                } else {
                    $variables[] = $variable;
                }
            }
        }
        return $variables;
    }
}
