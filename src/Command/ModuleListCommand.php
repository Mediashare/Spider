<?php 
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use App\Controller\Output;
use App\Controller\Module;
use App\Entity\Config;


class ModuleListCommand extends Command
{
    protected static $defaultName = 'spider:module:list';

    private $output;
    public function __construct(ContainerInterface $container, Output $output) {
        $this->output = $output;
        parent::__construct();
        $this->container = $container;
    }

    protected function configure() {
        $this
        	// Helper
	        ->setDescription('Modules List.')
	        ->setHelp('Show modules list with description.')
    	;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $_SESSION['outputCli'] = $io;
        $this->output->banner();
        // Output
        echo $this->output->echoColor("******************\n", 'green');
        echo $this->output->echoColor("* Modules List");
        echo $this->output->echoColor("\n******************\n", 'green');
        $modules = $this->getModules();
        foreach ($modules as $module) {
            echo $this->output->echoColor("- ".$module->name, 'purple');
            echo $this->output->echoColor(" | ".$module->description."\n", 'blue');
        }
        echo "\n";
    }

    private function getModules() {
        $module = new Module();
        $modulesDir = $this->container->getParameter('modules_dir');
        $module->config = (object) null;
        $module->config->modulesDir = $modulesDir;
        $module->config->modules = true;
        $modules = $module->getModules();

        return $modules;
    }
}