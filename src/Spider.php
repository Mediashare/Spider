<?php
namespace Mediashare;

use Mediashare\Entity\Url;
use Mediashare\Entity\Config;
use Mediashare\Controller\Webspider;
session_start();

class Spider
{
    public $url;
    public $config;
    public $report;
    public function __construct(Url $url, Config $config) {
        $this->url = $url;
        $this->config = $config;
    }

    public function run() {
        $webspider = new Webspider($this->url, $this->config);
        $this->report = $webspider->run();
        return $this;
    }
}

