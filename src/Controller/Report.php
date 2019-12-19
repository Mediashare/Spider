<?php
namespace Mediashare\Controller;

use Mediashare\Entity\Config;
use Mediashare\Entity\Website;
use Mediashare\Service\Output;
use Mediashare\Service\FileSystem;
use Mediashare\Entity\Result;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class Report
{
   public $config;
   public $output;
   public function __construct(Config $config, Output $output) {
      $this->config = $config;
      $this->output = $output;
		$encoders = [new XmlEncoder(), new JsonEncoder()];
      $normalizers = new ObjectNormalizer();
      // $normalizers->setCircularReferenceLimit(1);
      $this->serializer = new Serializer([$normalizers], $encoders);
      $this->fileSystem = new FileSystem();
   }

   public function endResponse(Website $website) {
      $file = $this->create($website, $end = true);
      if (!empty($_SESSION['outputCli'])) { // Classic bin/console execution
         $outputCli = $_SESSION['outputCli'];
         $outputCli->text($this->output->echoColor("**********************", 'green'));
         $outputCli->text($this->output->echoColor("* Output file result: ",'white').$this->output->echoColor($file['fileDir'],'green'));
         $outputCli->text($this->output->echoColor("**********************", 'green'));
   
         if ($this->config->json) {
            $output = $_SESSION['outputCli'];
            $output->text($this->output->echoColor("***************", 'green'));
            $output->text($this->output->echoColor("* Json result: ",'cyan'));
            $output->text($this->output->echoColor("***************", 'green'));
            echo new Response($file['json'], 200, ['Content-Type' => 'application/json']);
            // echo new JsonResponse($json);
         }
      } else {
         if ($this->config->html) { // Else Spider Class used
            echo $this->output->echoColor("**********************\xA", 'green');
            echo $this->output->echoColor("* Output file result: ",'white').$this->output->echoColor($file['fileDir']."\xA",'green');
            echo $this->output->echoColor("**********************\xA", 'green');   
         } 
         if ($this->config->json) {
            if ($this->config->html) {
               echo $this->output->echoColor("***************\xA", 'green');
               echo $this->output->echoColor("* Json result: \xA",'cyan');
               echo $this->output->echoColor("***************\xA", 'green');
            }
            echo new Response($file['json'], 200, ['Content-Type' => 'application/json'])."\xA";
            // echo new JsonResponse($json);
         }
      }
      return $file;
   }

   public function create(Website $website) {
      $domain = $website->getDomain();
      $fileDir = $this->config->getReportsDir().$domain.'/'.$this->config->getId().'.json';
      
      $report = $this->build($website);
      $json = $this->serializer->serialize($report, 'json', [
         'circular_reference_handler' => function ($object) {
            return $object->getId();
         }
      ]);

      $this->fileSystem->createJsonFile($json, $fileDir);

      return [
         'json' => $json,
         'fileDir' => $fileDir
      ];
   }

   /**
    * Build json report
    *
    * @param Website $website
    * @return Result 
    */
   private function build(Website $website): Result {
      $result = new Result($this->config, $website);
      $result = $result->build();
      return $result;
   }
}
