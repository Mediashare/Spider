<?php
namespace Mediashare;

use Mediashare\Entity\Url;
use Mediashare\Entity\Config;
use Mediashare\Controller\Webspider;
session_start();

class Spider
{
    public $config;
    public $url;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function run(string $url) {
        $this->url = new Url($url);
        $config = $this->config->setUrl($url);

        $webspider = new Webspider($config);
        $reports = $webspider->run();
        
        return $reports;
    }
}

