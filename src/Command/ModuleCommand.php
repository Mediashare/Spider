<?php
namespace Mediashare\Spider\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModuleCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'spider:module';

    protected function configure()
    {
        $this
            ->setDescription('Generate module.')
            ->setHelp('Generate module with boilerplate.')
            // Arguments & options
            ->addArgument('name', InputArgument::REQUIRED, 'Module name')
            ->addOption('modulesDir', 'm', InputOption::VALUE_REQUIRED, 'Modules directory', './modules/')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $module = [
            'name' => $input->getArgument('name'),
            'modulesDir' => \rtrim($input->getOption('modulesDir'), '/').'/',
        ];
        $module = $this->create($module);
        echo "[Module created] ".$module["className"]." was created: ".$module['moduleFile']." \n";
        return 0;
    }
    private function create(array $module) {
        $module = $this->config($module);
        $boilerplate = $this->getBoilerplate($module);
        $module = $this->createFile($module, $boilerplate);
        return $module;
    }

    private function config(array $module) {
        $name = $module['name'];
        // Check if Module exist already
        $className = ucfirst(preg_replace( '/[^A-Za-z0-9]+/', '', $name));
        $moduleFile = $module['modulesDir'].$className.'.php';
        $filesystem = new Filesystem();
        $moduleExist = $filesystem->exists($moduleFile);
        if ($moduleExist) {
            $className = ucfirst(preg_replace( '/[^A-Za-z0-9]+/', '', $name.uniqid()));
            $moduleFile = $module['modulesDir'].$className.'.php';
            echo "[Module generator] ".$module["name"]." was already used. This module was rename to ".$className." \n";
        }

        return [
            'name' => $name,
            'className' => $className,
            'moduleFile' => $moduleFile,
        ];
    }

    private function getBoilerplate(array $module) {
        $boilerplate = __DIR__.'/../Boilerplate/module.bp';
        $boilerplate = file_get_contents($boilerplate);
        $boilerplate = str_replace('%$className%', $module['className'], $boilerplate);
        return $boilerplate;
    }

    private function createFile(array $module, string $boilerplate) {
        $filesystem = new Filesystem();
        try {
            $filesystem->dumpFile($module['moduleFile'], $boilerplate);
            return $module;
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your module file at ".$exception->getPath();
        }
    }
}