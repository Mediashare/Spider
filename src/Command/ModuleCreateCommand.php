<?php 
namespace Spider\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Spider\Controller\Output;
use Spider\Entity\Config;


class ModuleCreateCommand extends Command
{
    protected static $defaultName = 'spider:module:create';

    private $output;
    public function __construct(ContainerInterface $container, Output $output) {
        $this->output = $output;
        parent::__construct();
        $this->container = $container;
        $this->modulesDir = $container->getParameter('modules_dir');
    }

    protected function configure() {
        $this
        	// Helper
	        ->setDescription('Create new Module.')
	        ->setHelp('Operating Module System for Spider')
	        // Arguments
        	->addArgument('name', InputArgument::REQUIRED, 'Name of module')
        	->addOption('variables', '-i', InputOption::VALUE_NONE, 
        		'Active variables injections')
            ->addOption('author', '-a', InputOption::VALUE_REQUIRED, 
                'Module author (Anonymous by default).')
    	;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $_SESSION['outputCli'] = $io;
        $this->output->banner();
        
        if ($input->getOption('variables')) {$variables = "true";} else {$variables = "false";}
        if ($input->getOption('author')) {$author = $input->getOption('author');} else {$author = "Anonymous";}
        $module = [
            'name' => $input->getArgument('name'),
            'variables' => $variables,
            'author' => $author
        ];
        $module = $this->create($module);
        // Output
        echo $this->output->echoColor("******************\n", 'green');
        echo $this->output->echoColor("* Module created by ".$module['author']);
        echo $this->output->echoColor("\n******************\n", 'green');
        echo $this->output->echoColor("* Your Module name: ".$module['name']."\n");
        echo $this->output->echoColor("* Variables Enable: ".$module['variables']."\n");
        echo $this->output->echoColor("* Your Module file: ".$module['moduleFile']."\n");
        echo $this->output->echoColor("* Write your php code in function run()\n\n");
    }

    private function create(array $module) {
        $module = $this->config($module);
        $boilerplate = $this->getBoilerplate();
        $boilerplate = $this->rewriteBoilerplate($module, $boilerplate);
        $module = $this->createFile($module, $boilerplate);
        return $module;
    }

    private function config(array $module) {
        $name = $module['name'];
        // Check if Module exist already
        $className = ucfirst(preg_replace( '/[^A-Za-z0-9]+/', '', $name));
        $moduleFile = $this->modulesDir.$className.'.php';
        $filesystem = new Filesystem();
        $moduleExist = $filesystem->exists($moduleFile);
        if ($moduleExist) {
            $className = ucfirst(preg_replace( '/[^A-Za-z0-9]+/', '', $name.uniqid()));
            $moduleFile = $this->modulesDir.$className.'.php';
        }

        // Author Normalization
        $author = ucfirst($module['author']);

        return [
            'name' => $name,
            'author' => $author,
            'className' => $className,
            'moduleFile' => $moduleFile,
            'variables' => $module['variables'],
            'description' => ""
        ];
    }

    private function getBoilerplate() {
        $boilerplate = $this->modulesDir.'../Boilerplate/module.bp';
        $boilerplate = file_get_contents($boilerplate);

        return $boilerplate;
    }

    private function rewriteBoilerplate(array $module, string $boilerplate) {
        $boilerplate = str_replace('%$author%', $module['author'], $boilerplate);
        $boilerplate = str_replace('%$className%', $module['className'], $boilerplate);
        $boilerplate = str_replace('%$name%', $module['name'], $boilerplate);
        $boilerplate = str_replace('%$variables%', $module['variables'], $boilerplate);
        $boilerplate = str_replace('%$description%', $module['description'], $boilerplate);
        if (!isset($module['script'])) {$module['script'] = "public function run() {}";}
        $boilerplate = str_replace('%$script%', $module['script'], $boilerplate);
        
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