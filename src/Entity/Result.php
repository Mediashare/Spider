<?php

namespace Mediashare\Entity;

use Mediashare\Service\FileSystem;
use Mediashare\Controller\Webspider;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class Result
{
    public $id;
    public $config;
    public $website;
    public $modules = [];
    public $errors = [];
    public function __construct(Webspider $webspider) {
        $this->id = $webspider->config->getId();
        $this->config = $webspider->config;
        $this->modules = $webspider->modules->results;
        $this->website = $webspider->url->getWebsite();
        $this->errors = $webspider->errors;
    }

    public function build(bool $end = false) {
        $result = $this->create($end);
        return $this;
    }

    public function create(bool $end = false) {
       $json = $this->json();
       $output = $this->config->getOutput();
       $fileSystem = new FileSystem();
       $fileSystem->createJsonFile($json, $output);
       return $this;
    }
 
    public function json() {
        // Serialize
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = new ObjectNormalizer();
        $serializer = new Serializer([$normalizers], $encoders);
        $json = $serializer->serialize($this, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        return $json;
    }
}
