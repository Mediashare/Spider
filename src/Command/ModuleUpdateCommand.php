<?php 
namespace App\Command;

use App\Entity\Config;
use App\Entity\Url;
use App\Controller\Guzzle;
use App\Controller\Module;
use App\Controller\Output;
use Symfony\Component\DomCrawler\Crawler as Dom;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ModuleUpdateCommand extends Command
{
    protected static $defaultName = 'spider:module:update';

    private $output;
    public function __construct(ContainerInterface $container, Output $output) {
        $this->output = $output;
        parent::__construct();
        $this->container = $container;
    }

    protected function configure() {
        $this
        	// Helper
	        ->setDescription('Update/Download all modules.')
	        ->setHelp('Update/Download all modules.')
    	;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $_SESSION['outputCli'] = $io;
        $this->output->banner();
        // Output
        echo $this->output->echoColor("******************\n", 'green');
        echo $this->output->echoColor("* Modules Update");
        echo $this->output->echoColor("\n******************\n", 'green');
        $modules = $this->getModules();
        foreach ($modules as $module) {
            echo $this->output->echoColor("- ".$module->name, 'purple');
            echo $this->output->echoColor(" | ".$module->description."\n", 'blue');
        }
        echo "\n";
    }

    private function getModules() {
        $urls = $this->getUrls();
        dd($urls);
        // Download all file from $urls[]
        return $modules;
    }
    
    private function getUrls() {
        $url = "http://modules.webspider.fr/";
        $url = new Url($url);

        $guzzle = new Guzzle();
        $webpage = $guzzle->getWebPage($url);
        
        $dom = new Dom($webpage->getBody()->getContent());
		// Crawl links
		foreach($dom->filter('a') as $link) {
			if (!empty($link)) {
				$href = rtrim(ltrim($link->getAttribute('href')));
				if ($href && strpos($href, '.php')) {
					$urls[] = $url.$href;
				}
			}
		}
        return $urls;
    }
}