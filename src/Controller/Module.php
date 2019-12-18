<?php
namespace Mediashare\Controller;
use Mediashare\Controller\FileSystem;

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
            $this->website->modules[$module->className]['name'] = $name;
            $this->website->modules[$module->className]['description'] = $description;
            if ($result) {
                // dump($this->webpage->getUrl()->getUrl(),$result);
                $this->website->modules[$module->className]['results'][(string) $this->webpage->getUrl()] = $result;
            }
            if ($module->errors) {
                $this->website->errors = array_merge($this->website->errors, $module->errors);
            }
        }
    }
    
    public function getModules() {
        $moduleDir = $this->config->modulesDir;
        $modulesFiles = glob($moduleDir.'*.php');
        $modules = [];
        foreach($modulesFiles as $moduleFile) {
            require_once $moduleFile;
            $className = "Mediashare\\Modules\\".basename($moduleFile, '.php');
            $module = new $className();
            $module->className = basename($moduleFile, '.php');
            if ($this->config->all_modules === true || 
                (is_array($this->config->modules) && !empty($this->config->modules[0]) && $this->config->modules[0] === null)) { // If all module enabled (-m) 
                    $modules[$module->className] = $module;
            } elseif (is_array($this->config->modules) && count($this->config->modules) > 0) { // If specific module selected (-m Module_Name) 
                foreach ($this->config->modules as $module_enable):
                    if ($module_enable == $module->name || $module_enable == $module->className):
                        $modules[$module->className] = $module;
                    endif;                        
                endforeach;
            }
        }

        // If specific module was disabled (-d FileDownload)
        if (is_array($this->config->disable_modules) && count($this->config->disable_modules) > 0):
            foreach ($this->config->disable_modules as $module_disable):
                unset($modules[$module_disable]);                    
            endforeach;
        endif;

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
