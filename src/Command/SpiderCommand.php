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
            ->addArgument('urls', InputArgument::IS_ARRAY, 'Website url')
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
            ->addOption('enable-modules', 'M', InputOption::VALUE_NONE, 
                'Enable all modules.')
            ->addOption('module', 'm', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 
                'Enable specific module(s).')
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
        $config->setId($input->getOption('id'));
        $config->addUrls($input->getArgument('urls'));
        $config->setWebspider($input->getOption('webspider'));
        // Require & Exception in URL
        $config->setRequires((array) $input->getOption('require'));
        $config->setExceptions((array) $input->getOption('exception'));
        // Output
        $config->setReportsDir($this->container->getParameter('reports_dir'));
        $config->setModulesDir($this->container->getParameter('modules_dir'));
        $config->setJson($input->getOption('json'));
        $config->setOutput($input->getOption('output'));
        $config->setHtml($input->getOption('html'));
        $config->enableAllModule($input->getOption('enable-modules'));
        // Modules
        $config->addModules($input->getOption('module'));
        // Inject input variables in modules 
        $config->addVariables($input->getOption('inject-variable'));
        
        // Files
        $file = $input->getOption('file');
        if ($file) {
            $file_content = fopen($file, 'r');
            while (($line = fgets($file_content)) !== false) {
                $url = trim(preg_replace('/\s\s+/', ' ', $line));
                $url = new Url($url);
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

        return $config;
    }
}
