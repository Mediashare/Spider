<?php 
namespace App\Api\Docker;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Api\Docker;

// *********************
// Execution functions *
// *********************
class Execution extends Docker {

    public function command(string $name, string $arguments) {
        $image = "slote/webspider";
        $dockerCommand = "docker run -d -v " . $this->volume . " --name " . $name . " " . $this->image;
        $webspiderCommand = $this->webspiderCommand .' '. $arguments;
        $command = $dockerCommand . '  ' . $webspiderCommand;
        $result = $this->execute($command);

        return $result;
    }

    private function execute(string $command) {
        $process = new Process($command);
        $process->setWorkingDirectory(getcwd() . "/../");
        $process->run();
        if (!$process->isSuccessful()) {throw new ProcessFailedException($process);}
        
        $output = (string) $process->getOutput();
        $result = trim(preg_replace('/\s+/', ' ', $output));
        
        return $result;
    }
}
