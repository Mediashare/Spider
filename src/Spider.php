<?php
namespace Mediashare\Spider;

use Mediashare\Spider\Entity\Url;
use Mediashare\Spider\Entity\Config;
use Mediashare\Spider\Controller\Webspider;

class Spider
{
    public $url;
    public $config;
    public function __construct(Url $url, Config $config) {
        $this->url = $url;
        $this->config = $config;
    }

    public function run() {
        $webspider = new Webspider($this->url, $this->config);
        $report = $webspider->run();
        return $report;
    }
}

