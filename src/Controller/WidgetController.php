<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Module;

class WidgetController extends AbstractController
{
    public function __construct(Module $module) {
        $this->module = new Module();
    }
    
    public function form() {
        $modules = $this->module->getModules();
        return $this->render('webspider/_form.html.twig', [
            'modules' => $modules
        ]);
    }

    // Report Widgets
    public function spiderStatus(string $website, string $id, array $spider, $refresh = false) {
        return $this->render('webspider/_status.html.twig', [
            'website' => $website,
            'id' => $id,
            'spider' => $spider,
            'refresh' => $refresh
        ]);
    }
    public function info(array $config, string $website, string $id, string $url = null) {
        return $this->render('report/_info.html.twig', [
            'website' => $website,
            'id' => $id,
            'config' => $config,
            'url' => $url
        ]);
    }
    public function urls(array $urls, string $website, string $id) {
        $results = null;
        foreach ($urls as $url => $result) {
            $results[$url] = $result;
            $results[$url]['color'] = 'primary';
            if (isset($result['header']) && isset($result['header']['httpCode'])) {
                $results[$url]['color'] = $this->getColor($result['header']['httpCode']);
            }
        }
        return $this->render('report/url/_urls.html.twig', [
            'urls' => $results,
            'website' => $website,
            'id' => $id
        ]);
    }
    public function errors(array $errors) {
        return $this->render('report/_errors.html.twig', [
            'errors' => $errors,
        ]);
    }
    public function modules(array $modules, string $website, string $id) {        
        foreach ((array) $modules as $name => $module) {
            $modules[$name]['name'] = $module['name'];
            $modules[$name]['description'] = $module['description'];
        }
        return $this->render('report/module/_modules.html.twig', [
            'website' => $website,
            'id' => $id,
            'modules' => $modules,
        ]);
    }
    public function variables(array $config, string $website) {
        return $this->render('report/module/_variables.html.twig', [
            'website' => $website,
            'variablesInjected' => $config['variables-injected'],
        ]);
    }
    public function httpCodes(array $urls) {
        $total = 0;
        $httpCodes = [];
        foreach ($urls as $url) {
            if (isset($url['header'])) {
                $httpCode = (string) $url['header']['httpCode'];
                if ($httpCode) {
                    if (!isset($httpCodes[$httpCode])) {
                        $color = $this->getColor($httpCode);
        
                        $httpCodes[$httpCode] = [
                            'count' => 0,
                            'color' => $color
                        ];
                    }
                    $httpCodes[$httpCode]['count']++;
                    $total++;
                }
            }
        }
        
        return $this->render('report/_httpCodes.html.twig', [
            'httpCodes' => $httpCodes,
            'total' => $total   
        ]);
    }
    public function timers(array $urls) {
        $results = [];
        foreach ((array) $urls as $url => $header) {
            if(isset($header['header']) && isset($header['header']['httpCode'])) {
                $name = $header['header']['httpCode'];
                $results[$name]['name'] = $name;
                $results[$name]['color'] = $this->getColor($name);
                $results[$name]['data'][] = [
                    (int) $header['header']['transferTime'],
                    (int) $header['header']['downloadSize'],
                    ['url' => (string) $url],
                ];
            }
        }
        return $this->render('report/_timers.html.twig', [
            'urls' => $results,
        ]);
    }
    // Report Url
    public function sources(string $website, string $id, array $urls, array $sources) {
        $results = [];
        foreach ($sources as $source) {
            $url = $urls[$source];
            $results[$source] = $url;
            $results[$source]['color'] = 'primary';
            if (isset($url['header']) && isset($url['header']['httpCode'])) {
                $results[$source]['color'] = $this->getColor($url['header']['httpCode']);
            }
        }

        return $this->render('report/url/_urls.html.twig', [
            'urls' => $results,
            'website' => $website,
            'id' => $id
        ]);
    }
    // Report Url Module
    public function module(array $module, string $website, string $id) {
        $description = null;
        if (isset($module['description'])) {$description = $module['description'];}
        $results = [];
        foreach ((array) $module['results'] as $key => $value) {
            if (is_array($value)) {
                $parent = $key;$values = $value;
                foreach ($values as $key => $value) {
                    $results[] = ['parent' => $parent,'key' => $key,'value' => $value];
                }
            } else {$results[] = ['key' => $key,'value' => $value];}
        }
        
        return $this->render('report/module/_module.html.twig', [
            'name' => $module['name'],
            'description' => $description,
            'results' => $results,
            'website' => $website,
            'id' => $id
        ]);
    }

    private function getColor(string $httpCode) {
        $color = 'primary';
        if ($httpCode[0] <= 2) {
            $color = 'success';
        } elseif ($httpCode[0] == 3) {
            $color = 'warning';
        } elseif ($httpCode[0] >= 4) {
            $color = 'danger';
        }
        return $color;
    }
}
