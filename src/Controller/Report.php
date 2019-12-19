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
   public function __construct(Config $config) {
      $this->config = $config;
      $this->output = new Output($config);
		$encoders = [new XmlEncoder(), new JsonEncoder()];
      $normalizers = new ObjectNormalizer();
      // $normalizers->setCircularReferenceLimit(1);
      $this->serializer = new Serializer([$normalizers], $encoders);
      $this->fileSystem = new FileSystem();
   }


   public function create(Website $website, bool $end = false) {
      $domain = $website->getDomain();
      $file_direction = $this->config->getReportsDir().$domain.'/'.$this->config->getId().'.json';
      
      $report = $this->build($website);
      $json = $this->serializer->serialize($report, 'json', [
         'circular_reference_handler' => function ($object) {
            return $object->getId();
         }
      ]);

      $this->fileSystem->createJsonFile($json, $file_direction);
      if ($end) {
         $this->output->fileDirection($file_direction);
         $this->output->json($json);
      }
      return $report;
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
