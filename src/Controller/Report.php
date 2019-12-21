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
   public $webspider;
   public $config;
   public $website;
   public $result;
   public function __construct(Webspider $webspider) {
      $this->webspider = $webspider;
      $this->config = $webspider->config;
      $this->website = $webspider->config->getUrl()->getWebsite();
      $this->output = new Output($webspider->config);
   }

   /**
    * Build json report
    *
    * @param Website $website
    * @return Result 
    */
    public function build() {
      $result = new Result($this->webspider);
      $this->result = $result->build();
      return $this->result;
   }

   public function create(bool $end = false) {
      $json = $this->json($this->result);
      $output = $this->config->getOutput();
      $fileSystem = new FileSystem();
      $fileSystem->createJsonFile($json, $output);
      if ($end) {
         $this->output->fileDirection($output);
         $this->output->json($json);
      }
      return $this;
   }

   public function json(Result $result) {
      // Serialize
		$encoders = [new XmlEncoder(), new JsonEncoder()];
      $normalizers = new ObjectNormalizer();
      $serializer = new Serializer([$normalizers], $encoders);
      $json = $serializer->serialize($result, 'json', [
         'circular_reference_handler' => function ($object) {
            return $object->getId();
         }
      ]);
      return $json;
   }
}
