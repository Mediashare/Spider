<?php
namespace Mediashare\Service;

use Mediashare\Entity\Url;
use Mediashare\Entity\Config;
use Mediashare\Entity\Website;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Output client
 */
class Output {
    public $config;
    public $verbose;
    public function __construct(Config $config) {
        $this->config = $config;
        $this->verbose = $config->getVerbose();
    }
    
	/**
	 * Echo Banner
	 */
    public function banner() {
        if ($this->verbose) {
            echo $this->echoColor("\n______________________________________________\n", 'white');
            echo $this->echoColor("|                                             |\n", 'white');
            echo $this->echoColor("|                  ", 'white').$this->echoColor("WebSpider").$this->echoColor("                  |\n", 'white');
            echo $this->echoColor("|                   -------                   |\n", 'white');
            echo $this->echoColor("|_____________________________________________|\n", 'white');
            echo $this->echoColor("                                   | by ", 'white').$this->echoColor("Slote").$this->echoColor(" |\n", 'white');
            echo $this->echoColor("                                   |__________/\n", 'white');
            echo $this->echoColor("\n", 'white');
        }
    }

    /**
     * Color string for output client
     * @param  string $txt
     * @param  string|null $color
     * @return string
     */
    public function echoColor(string $txt, string $color = null) {
        if ($this->verbose) {
            if (!$color) {$idColor = rand(30,37);} else {$idColor = $this->translateColor($color);}
            return "\033[".$idColor."m".$txt."\033[39m";
        }
    }

    
	public function progress(Website $website, $webpage, Url $url) {
        if ($this->verbose) {
            $counter = count($website->getUrlsCrawled()) + 1;
            $max_counter = (count($website->getUrlsCrawled()) + count($website->getUrlsNotCrawled()));
            
            if ($webpage) {$requestTime = $webpage->getHeader()->getTransferTime()."ms";} else {$requestTime = null;}
            $message = $this->echoColor("--- (".$counter."/".$max_counter.") URL: [".$url->getUrl()."] ".$requestTime." ---", "white");
            // ProgressBar
            $this->progressBar($counter, $max_counter, $message);
        }
    }

    public function progressBar(int $counter, int $max_counter, ?string $message) {
        if ($this->verbose) {
            $climate = new \League\CLImate\CLImate;
            // $climate->clear();
            // Progress Status
            $pourcent = ($counter/$max_counter) * 100;
            if ($pourcent >= 90):
                $climate->green();
            elseif ($pourcent >= 75):
                $climate->lightGreen();
            elseif ($pourcent >= 50):
                $climate->blue();
            else:
                $climate->cyan();        
            endif;
            $progress = $climate->progress()->total($max_counter);        
            $progress->advance($counter, $message);
        }
    }

    public function fileDirection(string $file_direction) {
        if ($this->config->getVerbose()) {
            $climate = new \League\CLImate\CLImate;
            $climate->clear();
            $climate->border('-*-', 50)->animation('right');
            echo $this->echoColor("* Output file result: ",'white').$this->echoColor($file_direction."\xA",'green');  
            $climate->border('-*-', 50);
        }
    }

    public function json($json) {
        if ($this->config->getVerbose()) {
            if ($this->config->getJson()) {
                echo $this->echoColor("***************\xA", 'green');
                echo $this->echoColor("* Json result: \xA",'cyan');
                echo $this->echoColor("***************\xA", 'green');
                echo new Response($json, 200, ['Content-Type' => 'application/json'])."\xA";
                $climate = new \League\CLImate\CLImate;
                $climate->border('-*-');
            }
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