<?php
namespace Mediashare\Service;

use Mediashare\Entity\Url;
use Mediashare\Entity\Config;
use Mediashare\Entity\Website;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Output client
 */
class Output extends Controller
{
    public $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }
    
	/**
	 * Echo Banner
	 */
    public function banner() {
        echo $this->echoColor("\n______________________________________________\n", 'white');
        echo $this->echoColor("|                                             |\n", 'white');
        echo $this->echoColor("|                  ", 'white').$this->echoColor("WebSpider").$this->echoColor("                  |\n", 'white');
        echo $this->echoColor("|                   -------                   |\n", 'white');
        echo $this->echoColor("|_____________________________________________|\n", 'white');
        echo $this->echoColor("                                   | by ", 'white').$this->echoColor("Slote").$this->echoColor(" |\n", 'white');
        echo $this->echoColor("                                   |__________/\n", 'white');
        echo $this->echoColor("\n", 'white');
    }

    /**
     * Color string for output client
     * @param  string $txt
     * @param  string|null $color
     * @return string
     */
    public function echoColor(string $txt, string $color = null) {
        if (!$color) {$idColor = rand(30,37);} else {$idColor = $this->translateColor($color);}
        return "\033[".$idColor."m".$txt."\033[39m";
    }

	public function progress(Website $website, $webPage, Url $url) {
        // ProgressBar
        $counter = count($website->getUrlsCrawled()) + 1;
        $max_counter = (count($website->getUrlsCrawled()) + count($website->getUrlsNotCrawled()));
		if ($webPage) {$requestTime = $webPage->getHeader()->getTransferTime()."ms";} else {$requestTime = null;}
		if ($this->config->html) {
			$message = $this->echoColor("--- (".$counter."/".$max_counter.") URL: [".$url->getUrl()."] ".$requestTime." --- \n", 'cyan');
			echo $message;
		} elseif (!$this->config->json) {
			$message = $this->echoColor("--- URL: [".$url->getUrl()."] ".$requestTime." ---", 'cyan');
			$this->progressBar($counter, $max_counter, $message);
		}
    }
    
    public function progressBar(int $index, int $max, string $message = "") {
        if (isset($_SESSION['outputCli'])) {
            $outputCli = $_SESSION['outputCli'];
            $progressBar = new ProgressBar($outputCli, $max);
            // if (strlen($message) > 70)
            //     $message = substr($message, 0, 70) . '...';
            // $progressBar->setBarWidth(10);
            $progressBar->setMessage($message, 'message');
            $progressBar->setBarCharacter('<fg=white>⚬</>');
            $progressBar->setEmptyBarCharacter("<fg=red>⚬</>");
            $progressBar->setProgressCharacter("<fg=cyan>➤</>");
            $progressBar->setFormat("%message% \n %current%/%max% [%bar%] 🏁 %percent:3s%% %memory:6s%");
            $progressBar->start();
            
            $progressBar->advance($index);
            if ($index >= $max) {$progressBar->finish();}
        }
    }

    private function translateColor(string $color) {
        $tabColor = [
            'black' => '0;30',
            'blue' => '0;34',
            'green' => '0;32',
            'cyan' => '0;36',
            'red' => '0;31',
            'purple' => '0;35',
            'brown' => '0;33',
            'yellow' => '1;33',
            'white' =>  '1;37'
        ];
        
        return $tabColor[$color];
    }
}