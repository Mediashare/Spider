<?php 
namespace Mediashare\Command;

use Mediashare\Entity\Url;
use Mediashare\Entity\Config;
use Mediashare\Controller\Guzzle;
use Mediashare\Controller\Module;
use Mediashare\Service\Output;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DomCrawler\Crawler as Dom;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class ModuleUpdateCommand extends Command
{
    protected static $defaultName = 'spider:module:update';

    private $output;
    public function __construct(ContainerInterface $container, Output $output) {
        $this->output = $output;

        $url = "http://modules.webspider.fr/";
        $this->url = new Url($url);
        $this->guzzle = new Guzzle();
        
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
            echo $this->output->echoColor("- ".$module['name']."\n", 'purple');
        }
        echo "\n";
    }

    private function getModules() {
        $webpage = $this->guzzle->getWebPage($this->url);
        $dom = new Dom($webpage->getBody()->getContent());
		// Crawl links
		foreach($dom->filter('a') as $link) {
			if (!empty($link)) {
				$href = rtrim(ltrim($link->getAttribute('href')));
				if ($href && (strpos($href, '.php') || strpos($href, '.md'))) {
					$modules[] = [
                        'name' => $href,
                        'url' => $this->url.$href,
                    ];
				}
			}
		}
        
        $modules = $this->download($modules);
        $create = $this->create($modules);
        return $modules;
    }

    // Download all file from $urls[]
    private function download(array $modules) {
        foreach ($modules as $key => $module) {
            $name = $module['name'];
            $url = new Url($module['url']);
            $webpage = $this->guzzle->getWebPage($url);
            $dom = new Dom($webpage->getBody()->getContent());
            $modules[$key]['content'] = $webpage->getBody()->getContent();
        }
        return $modules;
    }

    private function create(array $modules) {
        $filesystem = new Filesystem();
        foreach ($modules as $module) {
            try {
                $filesystem->dumpFile($this->container->getParameter('modules_dir') . $module['name'], $module['content']);
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your module file at ".$exception->getPath();
            }
        }
        return $modules;
    }
}