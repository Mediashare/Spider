<?php
namespace App\Service;

use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Output client
 */
class Output
{
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

    public function progressBar(int $index, int $max, string $message = "") {
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