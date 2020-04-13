<?php
namespace Mediashare\Spider\Entity;

use Zumba\JsonSerializer\JsonSerializer;
use Mediashare\Spider\Service\FileSystem;
use Mediashare\Spider\Controller\Webspider;

class Result
{
    public $id;
    public $config;
    public $crawler;
    public $modules = [];
    public $errors = [];
    public function __construct(Webspider $webspider) {
        $this->id = $webspider->config->getId();
        $this->config = $webspider->config;
        $this->crawler = $webspider->crawler;
        $this->modules = $webspider->crawler->modules;
        unset($webspider->crawler->modules);
        $this->errors = $webspider->errors;
    }

    /**
     * Create Json fil Report 
     *
     * @return self
     */
    public function build(): self {
        $json = $this->json($this);
        $output = $this->config->getOutput();
        $fileSystem = new FileSystem();
        $fileSystem->createJsonFile($json, $output);
        return $this;
    }
 
    public function json($object): string {
        $serializer = new JsonSerializer();
        $json = $serializer->serialize($object);
        return $json;
    }
}
