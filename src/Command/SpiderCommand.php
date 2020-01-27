<?php
namespace Mediashare\Spider\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mediashare\Spider\Entity\Config;
use Mediashare\Spider\Entity\Url;
use Mediashare\Spider\Spider;

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
            ->addOption('webspider', 'w', InputOption::VALUE_OPTIONAL, 'Crawl one page', true)
            ->addOption('pathRequires', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Path requires', [])
            ->addOption('pathExceptions', 'e', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Path exceptions', [])
            ->addOption('reportsDir', 'R', InputOption::VALUE_REQUIRED, 'Reports directory', __DIR__.'/../../reports/')
            ->addOption('modulesDir', 'm', InputOption::VALUE_REQUIRED, 'Modules directory', __DIR__.'/../../modules/')
            ->addOption('kernelModules', 'k', InputOption::VALUE_OPTIONAL, 'Disable kernel modules', true)
            ->addOption('removeModule', 'rm', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Remove module(s)', [])
            ->addOption('json', 'j', InputOption::VALUE_OPTIONAL, 'Json output', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get Inputs
        $url = $input->getArgument('url');
        $webspider = $input->getOption('webspider');
        if ($webspider !== true): $webspider = false; endif;
        $pathRequires = $input->getOption('pathRequires');
        $pathExceptions = $input->getOption('pathExceptions');
        $json = $input->getOption('json');
        if ($json !== false): $json = true; endif;
        $reportsDir = $input->getOption('reportsDir');
        $modulesDir = $input->getOption('modulesDir');
        $kernelModules = $input->getOption('kernelModules');
        if ($kernelModules !== true): $kernelModules = false; endif;
        $removeModule = $input->getOption('removeModule');
        

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