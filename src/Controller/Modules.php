<?php
namespace Mediashare\Controller;

use Mediashare\Entity\Url;
use Mediashare\Entity\Config;
use Mediashare\Controller\Crawler;
use Mediashare\Controller\FileSystem;

class Modules {
    public $results = [];
    public $errors = [];
    public function __construct(Config $config) {
        $this->config = $config;
    }
    
    public function run(Url $url) {
        $crawler = new Crawler($url, $this->config);
        $crawler = $crawler->run();
        $results = [];
        // Modules
        $modules = $this->getModules($this->config);
        foreach ($modules as $module) {
            // Set required Object in Module
            $module->url = $url;
            $module->config = $crawler->config;
            $module->crawler = $crawler->crawler;
            // SEO
            $name = $module->name;
            $description = $module->description;

            // Execute this Module
            $result = $module->run();
            
            // Report
            $this->results[$module->className]['name'] = $module->name;
            $this->results[$module->className]['description'] = $module->description;
            if ($result) {
                // dump($this->webpage->getUrl()->getUrl(),$result);
                $this->results[$module->className]['results'][(string) $crawler->url->getUrl()] = $result;
            }
            if (!empty($module->errors)) {
                $this->results[$module->className]['errors'][] = $module->errors;
                foreach ($module->errors as $key => $error) {
                    $this->errors[] = $error;
                }
            }
        }

        // For Memory size
        $url->getWebpage()->setBody(null);

        return $this;
    }

    
    /**
     * Get all modules from $moduleDir & automatique including that class.
     *
     * @return array
     */
    public function getModules(Config $config) {
        $moduleDir = $config->getModulesDir();
        $modulesFiles = glob($moduleDir.'*.php');
        $modules = [];
        foreach($modulesFiles as $moduleFile) {
            require_once $moduleFile;
            $className = "Mediashare\\Modules\\".basename($moduleFile, '.php');
            $module = new $className();
            $module->className = basename($moduleFile, '.php');
            if ($config->enableModules) { // If all module enabled (-m) 
                    $modules[$module->className] = $module;
            } elseif (is_array($config->modules) && count($config->modules) > 0) { // If specific module selected (-m Module_Name) 
                foreach ($config->modules as $module_enable):
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
