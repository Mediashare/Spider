<?php 
namespace Mediashare\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Mediashare\Service\Output;
use Mediashare\Controller\Webspider;
use Mediashare\Entity\Config;
use Mediashare\Entity\Url;
use Mediashare\Entity\Website;

class SpiderCommand extends Command
{
    protected static $defaultName = 'spider:run';

    private $output;
    private $webspider;
    public function __construct(ContainerInterface $container, Output $output) {
        $this->output = $output;
        parent::__construct();
        $this->container = $container;
    }

    protected function configure() {
        $this
        	// Helper
	        ->setDescription('Execute Web Crawler')
	        ->setHelp('This command crawl website pages.')
	        // Arguments
            ->addArgument('url', InputArgument::IS_ARRAY, 'Website url')
            // Options
            //  General
        	->addOption('webspider', 'w', InputOption::VALUE_NONE, 
        		'If you want crawl all pages on this website.')
            //  Require & Exception in URL  
            ->addOption('require', 'R', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 
        		'Add path require. (-R foo -R bar).')
        	->addOption('exception', 'E', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 
                'Add exception. If url contains one of these words then not crawled. (-E foo -E bar).')
            //  Input
        	->addOption('file', 'f', InputOption::VALUE_REQUIRED, 
                'Read all urls in file submited.')
            //  Output
        	->addOption('json', 'j', InputOption::VALUE_NONE, 
                'Return json response in terminal.')
        	->addOption('output', 'o', InputOption::VALUE_REQUIRED, 
                'Output path destination.')
            ->addOption('id', false, InputOption::VALUE_REQUIRED, 
                'Id (name) Report.')
            // Modules
            ->addOption('all_modules', 'a', InputOption::VALUE_NONE, 
                'Enable all modules.')
            ->addOption('modules', 'm', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 
                'Enable specific module(s).')
            ->addOption('disable_modules', 'd', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 
                'Disable specific module(s) or disable all modules if not module specified.')
            // Inject modules variables 
            ->addOption('inject-variable', 'i', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 
                'Inject input variables in specific module. json format (-i {"moduleName":["foo","bar"]}").')
            ->addOption('html', null, InputOption::VALUE_NONE, 
                    'Html output.')
            
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $_SESSION['outputCli'] = $io;
        
        $config = $this->initConfig($input);
        if (!$config->html) {$this->output->banner();}
        $webspider = new Webspider($config);
        $webspider->run();
    }

    protected function initConfig(InputInterface $input) {
        $config = new Config();

        if ($input->getOption('id')) {
            $config->setId($input->getOption('id'));
        }

        $file = $input->getOption('file');
        if ($file) {
            $file_content = fopen($file, 'r');
            while (($line = fgets($file_content)) !== false) {
                $newUrl = trim(preg_replace('/\s\s+/', ' ', $line));
                $url = new Url($newUrl);
                $config->addUrl($url);
                $website = $config->getWebsite($url);
                if ($website) {
                    $website->addUrl($url);
                } else {
                    $website = new Website($url);
                    $config->addWebsite($website);
                }
            }
        }
        foreach ((array) $input->getArgument('url') as $newUrl) {
            $url = new Url($newUrl);
            $config->addUrl($url);
            
            $website = $config->getWebsite($url);
            if ($website) {
                $website->addUrl($url);
            } else {
                $website = new Website($url);
                $config->addWebsite($website);
            }
        }

        $config->setWebspider($input->getOption('webspider'));
        // Require & Exception in URL
        $config->setRequires((array) $input->getOption('require'));
        $config->setExceptions((array) $input->getOption('exception'));
        // Output
        $config->reportsDir = $this->container->getParameter('reports_dir');
        $config->modulesDir = $this->container->getParameter('modules_dir');
        $config->json = $input->getOption('json');
        $config->output = $input->getOption('output');
        $config->html = $input->getOption('html');
        // Modules
        $config->modules = $input->getOption('modules');
        $config->all_modules = $input->getOption('all_modules');
        $config->disable_modules = $input->getOption('disable_modules');
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
