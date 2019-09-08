<?php 
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Controller\Output;
use App\Controller\Webspider;
use App\Entity\Config;
use App\Entity\Url;
use App\Entity\Website;


class WebSpiderCommand extends Command
{
    protected static $defaultName = 'webspider:run';

    private $output;
    private $webspider;
    public function __construct(ContainerInterface $container, Output $output, Webspider $webspider) {
        $this->output = $output;
        $this->webspider = $webspider;
        parent::__construct();
        $this->container = $container;
    }

    protected function configure() {
        $this
        	// Helper
	        ->setDescription('Execute Web Crawler')
	        ->setHelp('This command crawl website pages.')
	        // Arguments
        	->addArgument('url', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Website url')
            // Options
            //  General
        	->addOption('webspider', 'w', InputOption::VALUE_NONE, 
        		'If you want crawl all pages on this website')
            //  Require & Exception in URL  
            ->addOption('require', 'R', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 
        		'Add path require. (-R foo -R bar)')
        	->addOption('exception', 'E', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 
                'Add exception. If url contains one of these words then not crawled. (-E foo -E bar)')
            //  Output
        	->addOption('json', 'j', InputOption::VALUE_NONE, 
            'Return json response in terminal')
        	->addOption('output', 'o', InputOption::VALUE_REQUIRED, 
                'Output path destination')
            ->addOption('id', false, InputOption::VALUE_REQUIRED, 
                'Id Report')
            // Modules
            ->addOption('modules', 'm', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 
                'Enable specific module(s). If null disable all modules')
            // Inject modules variables 
            ->addOption('inject-variable', 'i', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 
                'Inject input variables in specific module. (-i \'{"moduleName":["foo","bar"]}\')')
            
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $_SESSION['outputCli'] = $io;
        $this->output->banner();

        $config = $this->initConfig($input);
        $this->webspider->run($config);
    }

    protected function initConfig(InputInterface $input) {
        $config = new Config();

        if ($input->getOption('id')) {
            $config->setId($input->getOption('id'));
        }

        foreach ((array) $input->getArgument('url') as $newUrl) {
            $url = new Url($newUrl);
            $config->addUrl($url);

            $website = $config->getWebsite($url);
            if ($website) {
                $website->addUrl($urlEntity);
            } else {
                $website = new Website($url);
                $config->addWebsite($website);
            }
        }

        $config->setWebspider($input->getOption('webspider'));
        // Require & Exception in URL
        $config->setPathRequire((array) $input->getOption('require'));
        $config->setPathException((array) $input->getOption('exception'));
        // Output
        $config->reportsDir = $this->container->getParameter('reports_dir');
        $config->modulesDir = $this->container->getParameter('modules_dir');
        $config->json = $input->getOption('json');
        $config->output = $input->getOption('output');
        // Modules
        $config->modules = $input->getOption('modules');
        if (empty($config->modules)) {$config->modules = true;} // Enable Modules by default
        // Inject input variables in modules 
        $config->variables = null;
        foreach ($input->getOption('inject-variable') as $variables) {
            $variables = json_decode($variables, true);
            foreach ((array) $variables as $module => $variable) {
                $config->variables[$module][] = $variable;
            }
        }

        return $config;
    }
}
