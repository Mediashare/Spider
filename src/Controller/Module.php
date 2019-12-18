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
            // SEO
            $name = $module->name;
            $description = $module->description;
            // Variables
            $variables = $module->variables;
            if ($variables) {
                $module->variables = [];
                if (isset($this->config->variables[$module->className])) {
                    $module->variables = (array) $this->config->variables[$module->className];
                }
            }
            // Execute this Module
            $result = $module->run();
            $this->report($module, $result);
        }
    }

    public function report($module, $result) {
        // Report
        $this->website->modules[$module->className]['name'] = $module->name;
        $this->website->modules[$module->className]['description'] = $module->description;
        if ($result) {
            // dump($this->webpage->getUrl()->getUrl(),$result);
            $this->website->modules[$module->className]['results'][(string) $this->webpage->getUrl()] = $result;
        }
        if ($module->errors) {
            $this->website->errors = array_merge($this->website->errors, $module->errors);
        }
    }
    
    /**
     * Get all modules from $moduleDir & automatique including that class.
     *
     * @return void
     */
    public function getModules() {
        $moduleDir = $this->config->getModulesDir();
        $modulesFiles = glob($moduleDir.'*.php');
        $modules = [];
        foreach($modulesFiles as $moduleFile) {
            require_once $moduleFile;
            $className = "Mediashare\\Modules\\".basename($moduleFile, '.php');
            $module = new $className();
            $module->className = basename($moduleFile, '.php');
            if ($this->config->enable_modules) { // If all module enabled (-m) 
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
        // if (is_array($this->config->disable_modules) && count($this->config->disable_modules) > 0):
        //     foreach ($this->config->disable_modules as $module_disable):
        //         unset($modules[$module_disable]);                    
        //     endforeach;
        // endif;
        return $modules;
    }
}
