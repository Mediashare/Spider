<?php
namespace Spider\Controller;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Spider\Controller\FileSystem;
use Spider\Controller\Output;
use Spider\Entity\Report as BuildReport;
use Spider\Entity\Website;

class Report
{
   public function __construct() {
		$encoders = [new XmlEncoder(), new JsonEncoder()];
      $normalizers = new ObjectNormalizer();
      // $normalizers->setCircularReferenceLimit(1);
      $this->serializer = new Serializer([$normalizers], $encoders);
      $this->fileSystem = new FileSystem();
      $this->output = new Output();
   }

   public function endResponse(Website $website) {
      $file = $this->create($website, $end = true);

      $output = new Output();
      $outputCli = $_SESSION['outputCli'];
      $outputCli->text($output->echoColor("**********************", 'green'));
      $outputCli->text($output->echoColor("* Output file result: ",'white').$output->echoColor($file['fileDir'],'green'));
      $outputCli->text($output->echoColor("**********************", 'green'));

      if ($website->getConfig()->json) {
         $output = $_SESSION['outputCli'];
         $output->text($this->output->echoColor("***************", 'green'));
         $output->text($this->output->echoColor("* Json result: ",'cyan'));
         $output->text($this->output->echoColor("***************", 'green'));
         echo new Response($file['json'], 200, ['Content-Type' => 'application/json']);
         // echo new JsonResponse($json);
      }
   }

   public function create(Website $website) {
      $domain = $website->getDomain();
      $report = $this->build($website);

      $config = $website->getConfig();
      $fileDir = $config->reportsDir.$domain.'/'.$config->getId().'.json';  
      // $fileDir = $config->projectDir.'/public/reports/'.uniqid().'.json';  
      if ($config->output) {$fileDir = $config->output;}
      
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

   private function build(Website $website, $end = false) {
      $report = new BuildReport();
      date_default_timezone_set('Europe/Paris');
      $date = new \DateTime();
      $date = $date->format('d/m/Y H:i:s');

      // Config
      $config = $website->getConfig();
      $report->config = [
         'id' => $config->getId(),
         'webspider' => $config->getWebspider(),
         'pathRequire' => $config->getPathRequire(),
         'pathException' => $config->getPathException(),
         'json' => $config->json,
         'output' => $config->output,
         'modules' => $config->modules,
         'variables-injected' => $config->variables,
         'createDate' => $date,
      ];

      // Spider
      $job = 'progress';
      if (count($website->getUrlsNotCrawled()) == 0): $job = 'finish'; endif;
      $report->spider = [
         'job' => $job,
         'urlsCrawled' => count($website->getUrlsCrawled()),
         'urlsNotCrawled' => count($website->getUrlsNotCrawled()),
      ];

      // Website
      $report->website = [
         'domain' => $website->getDomain(),
         'scheme' => $website->getScheme(),
      ];

      // Urls
      foreach ($website->getUrls() as $url) {
         // Add Url
         $report->urls[$url->getUrl()] = [
            'url' => $url->getUrl(),
            'isCrawled' => $url->isCrawled(),
            'isExcluded' => $url->isExcluded(),
            'sources' => $url->getSources($website)
         ];
         
         $webpage = $url->getWebPage();
         if ($webpage) {
            // Url Header
            $header = $url->getWebPage()->getHeader();
            $headers = [
               'httpCode' => $header->getHttpCode(),
               'transferTime' => $header->getTransferTime(),
               'downloadSize' => $header->getDownloadSize(),
               'headers' => $header->getContent()
            ];
            $report->urls[$url->getUrl()]['header'] = $headers;
            // Url links
            $links = [
               'links' => $webpage->getLinks(),
               'externalLinks' => $webpage->getExternalLinks(),
            ];
            $report->urls[$url->getUrl()]['links'] = $links;
            // Modules
            if (isset($webpage->modules)) {
               $report->urls[$url->getUrl()]['modules'] = $webpage->modules;
            }
         }
      }

      // Modules
      if (isset($website->modules)) {$report->modules = $website->modules;}

      // Errors
      if (isset($website->errors)) {$report->errors = $website->errors;}

      return $report;
   }
}
