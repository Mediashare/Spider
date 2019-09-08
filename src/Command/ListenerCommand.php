<?php 
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Controller\Output;
use App\Controller\FileSystem;
use App\Entity\Config;
use Symfony\Component\DependencyInjection\ContainerInterface;
// Command
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;


class ListenerCommand extends Command
{
    protected static $defaultName = 'webspider:listener';

    private $output;
    public function __construct(Output $output, KernelInterface $kernel, ContainerInterface $container) {
        $this->output = $output;
        parent::__construct();
        $this->kernel = $kernel;
        $this->container = $container;
    }

    protected function configure() {
        $this
        	// Helper
	        ->setDescription('Run WebSpider Listeneer. (required for webinterface)')
            ->setHelp('Active queue system for listen WebSpider webinterface.')
    	;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $_SESSION['outputCli'] = $io;
        $this->output->banner();
        // Output
        echo $this->output->echoColor("*********************\n", 'purple');
        echo $this->output->echoColor("* WebSpider Listener");
        echo $this->output->echoColor("\n*********************\n", 'purple');

        $queueDir = $this->container->getParameter('queue_dir').'*.json';
        $fileSystem = new FileSystem();

        while(true) {
            $queue = glob($queueDir);
            foreach ($queue as $filePath) {
                $spider = json_decode(file_get_contents($filePath), true);
                if (!$spider['executed']) {
                    echo $this->output->echoColor("* New Spider ", 'green');
                    echo $this->output->echoColor("[".basename($filePath)."]\n", 'blue');
                    $spider['executed'] = true;
                    $fileSystem->createJsonFile(json_encode($spider), $filePath);

                    $command = $spider['command'];
                    $command['--quiet'] = false;
                    $application = new Application($this->kernel);
                    $application->setAutoExit(false);
                    $command = new ArrayInput($command);
                    $output = new NullOutput();
                    $application->run($command);
                    
                    $remove = $fileSystem->remove($filePath);
                    echo $this->output->echoColor("* ", 'red');
                    echo $this->output->echoColor("[".basename($filePath)."] ", 'blue');
                    echo $this->output->echoColor("Spider Killed!\n", 'red');
                    echo $this->output->echoColor("**********\n", 'purple');
                }
            }
            sleep(5);
        }
    }
}