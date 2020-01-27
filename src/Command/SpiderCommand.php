<?php
namespace Mediashare\Spider\Command;

use Phar;
use Mediashare\Spider\Spider;
use Mediashare\Spider\Entity\Url;
use Mediashare\Spider\Entity\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SpiderCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'spider:run';

    protected function configure()
    {
        $this
            ->setDescription('Run crawler.')
            ->setHelp('Spider is crawler used for scraping informations.')
            // Arguments & options
            ->addArgument('url', InputArgument::REQUIRED, 'The url of entrypoint for crawler.')
            ->addOption('webspider', 'w', InputOption::VALUE_NONE, 'Crawl one page')
            ->addOption('pathRequires', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Path requires', [])
            ->addOption('pathExceptions', 'e', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Path exceptions', [])
            ->addOption('reportsDir', 'R', InputOption::VALUE_REQUIRED, 'Reports directory', './reports/')
            ->addOption('modulesDir', 'm', InputOption::VALUE_REQUIRED, 'Modules directory', './modules/')
            ->addOption('removeModule', 'rm', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Remove module(s)', [])
            ->addOption('json', 'j', InputOption::VALUE_NONE, 'Json output')
        ;

        if (empty(Phar::running())): // Disable Kernel module SEO for .phar
            $this->addOption('kernelModules', 'k', InputOption::VALUE_NONE, 'Disable kernel modules. (disabled by default for .phar running)');
        endif;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get Inputs
        $url = $input->getArgument('url');
        $webspider = $input->getOption('webspider');
        if (empty($webspider)): $webspider = true; else: $webspider = false; endif;
        $pathRequires = $input->getOption('pathRequires');
        $pathExceptions = $input->getOption('pathExceptions');
        $json = $input->getOption('json');
        if (empty($json)): $json = false; else: $json = true; endif;
        $reportsDir = $input->getOption('reportsDir');
        $modulesDir = $input->getOption('modulesDir');
        $removeModule = $input->getOption('removeModule');
        if (empty(Phar::running())):
            $kernelModules = $input->getOption('kernelModules');
            if (empty($kernelModules)): $kernelModules = true; else: $kernelModules = false; endif;
        else:
            // Disable Kernel module SEO for .phar
            $kernelModules = false;
        endif;

        // Config
        $config = new Config();
        $config->setWebspider($webspider);
        $config->setPathRequires($pathRequires);
        $config->setPathExceptions($pathExceptions);
        // Modules
        $config->setReportsDir($reportsDir);
        $config->setModulesDir($modulesDir);
        $config->enableDefaultModule($kernelModules);
        foreach ($removeModule as $module) {
            $config->removeModule($module);
        }
        // Prompt Console / Dump
        $config->setVerbose(true);
        $config->setJson($json);
        // Url
        $url = new Url($url);
        // Run Spider
        $spider = new Spider($url, $config);
        $result = $spider->run();
        return 0;
    }
}