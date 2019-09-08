<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\FileSystem;
use App\Api\Docker;
use App\Api\Docker\Image;
use App\Api\Docker\Container;
use App\Api\Docker\Execution;
use App\Controller\ModuleController;

class ApiController extends AbstractController
{
    public function __construct() {
        $this->docker = new Docker();
        $this->docker->image = new Image();
        $this->docker->container = new Container();
        $this->docker->execution = new Execution();
    }

    /**
     * Execute Webspider
     * @Route("/api/webspider/execute", name="api_webspider_execute")
     */
    public function execute(Request $request) {
        $website = $request->request->get('website');
        $id = $request->request->get('id');
        $arguments = $request->request->get('arguments');
        
        $redirect = $this->generateUrl('report_show', [
            'website' => $website,
            'id' => $id
        ]);
            
        // Execute docker
        $execution = $this->docker->execution->command($id, $arguments);

        // Save id Report
        $user = $this->getUser();
        if ($user) {
            $id = $user->addReport($id);
            if (!$id) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Id already submited.',
                    'redirect' => $redirect,
                ]); 
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->json([
            'status' => 'success'
        ]);
    }

    /**
     * Check Status of spider
     * @Route("/api/spider/status", name="api_spider_status")
     */
    public function spiderStatus(Request $request) {
        $website = $request->request->get('website');
        $id = $request->request->get('id');

        $report = $this->getReport($website, $id);
        $spider = $report['spider'];
        $status['job'] = $spider['job'];
        
        $total = $spider['urlsCrawled'] + $spider['urlsNotCrawled'];
        if ($total == $spider['urlsCrawled']) {$pourcent = 100;} else {$pourcent = (int) ($spider['urlsNotCrawled'] * 100 / $total);}
        $status['progress'] = [
            'pourcent' => $pourcent,
            'total' => $total,
            'urlsCrawled' => $spider['urlsCrawled'],
            'urlsNotCrawled' => $spider['urlsNotCrawled'],
        ];

        return $this->json($status);
    }

    private function getReport(string $website, string $id) {
        $reportFile = $this->getParameter('reports_dir').$website.'/'.$id.'.json';
        $fileSystem = new Filesystem();
        $exist = $fileSystem->exist($reportFile);
        if (!$exist) {return false;}
        $file = file_get_contents($reportFile);
        $report = json_decode($file, true);
        
        return $report;
    }
}
