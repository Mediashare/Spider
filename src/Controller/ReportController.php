<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

class ReportController extends AbstractController
{
    /**
     * @Route("/reports/{website}", name="report")
     */
    public function index(string $website = null) {
        $reports = $this->getReports($website);
        return $this->render('report/index.html.twig', [
            'reports' => $reports,
            'website' => $website
        ]);
    }

    /**
     * @Route("/report/{website}/r/{id}/m/{module}", name="report_show")
     */
    public function show(string $website, string $id, string $module = null) {
        // $security = $this->security($id);
        // if (!$security) {return $this->redirect($this->generateUrl('report'));}
        $report = $this->getReport($website, $id);
        return $this->render('report/show.html.twig', [
            'report' => $report,
            'website' => $website,
            'id' => $id,
            'module' => $module
        ]);
    }

    /**
     * @Route("/report/{website}/r/{id}/url", name="report_show_url")
     */
    public function showUrl(Request $request, string $website, string $id) {
        // $security = $this->security($id);
        // if (!$security) {return $this->redirect($this->generateUrl('report'));}
        $url = $request->request->get('url');
        $report = $this->getReport($website, $id);
        // Get modules by $url
        $modules = [];
        foreach ($report['modules'] as $key => $module) {
            if (isset($module['results'])) {
                $urls = $module['results'];
                if (isset($urls[$url])) {
                    $modules[] = [
                        'name' => $module['name'],
                        'description' => $module['description'],
                        'results' => $urls[$url]
                    ];
                }
            }
        }
        
        $url = $report['urls'][$url];

        return $this->render('report/url/show.html.twig', [
            'website' => $website,
            'id' => $id,
            'report' => $report,
            'modules' => $modules,
            'url' => $url,
        ]);
    }

    /**
     * @Route("/report/{website}/r/{id}/url/module", name="report_show_url_module")
     */
    public function showUrlByModule(Request $request, string $website, string $id) {
        // $security = $this->security($id);
        // if (!$security) {return $this->redirect($this->generateUrl('report'));}
        $url = $request->request->get('url');
        $module = $request->request->get('module');
        $report = $this->getReport($website, $id);
        // Get module by $url
        $module = [
            'name' => $module,
            'results' => $report['modules'][$module]['results'][$url]
        ];
        
        $url = $report['urls'][$url];

        return $this->render('report/module/show.html.twig', [
            'website' => $website,
            'id' => $id,
            'report' => $report,
            'module' => $module,
            'url' => $url,
        ]);
    }

    /**
     * @Route("/report/{website}/r/{id}/module/{module}", name="report_module")
     */
    public function module(string $website, string $id, string $module) {
        // $security = $this->security($id);
        // if (!$security) {return $this->redirect($this->generateUrl('report'));}
        $report = $this->getReport($website, $id);
        $module = $report['modules'][$module];
        
        return $this->render('report/module/index.html.twig', [
            'report' => $report,
            'module' => $module
        ]);
    }

    /**
     * @Route("/report/{website}/remove/{id}", name="report_remove")
     */
    public function remove(string $website, string $id) {
        // $security = $this->security($id);
        // if (!$security) {return $this->redirect($this->generateUrl('report'));}
        $report = $this->getParameter('reports_dir').$website.'/'.$id.'.json';
        $filesystem = new Filesystem();
        try {
            $filesystem->remove($report);
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while remove your ".$website." report at ".$exception->getPath();
        }
        
        $user = $this->getUser();
        $user->removeReport($id);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('report');
    }

    
    private function getReports(string $target = null) {
        $reports = [];
        $reportDir = $this->getParameter('reports_dir');
        $filesystem = new Filesystem();
        $exist = $filesystem->exists($reportDir);
        // if ($this->getUser()): $idReports = $this->getUser()->getReports(); endif;
        if ($exist) {
            foreach (glob($reportDir.'*') as $websiteDir) {
                $website = basename($websiteDir);
                if (!$target || $target === $website) {
                    $files = glob($websiteDir.'/*.json');
                    foreach ((array) $files as $file) {
                        $id = rtrim(basename($file), '.json');
                        // if (!$this->getUser() || in_array($id, $idReports)) {
                            $reportFile = $this->getReport($website, $id);
                            if ($reportFile) {
                                $report['spider'] = $reportFile['spider'];
                                $report['website'] = $reportFile['website'];
                                $report['config'] = $reportFile['config'];
                                $report['urls'] = count($reportFile['urls']);
                                foreach ((array) $reportFile['modules'] as $name => $module) {
                                    $report['modules'][$name]['name'] = $module['name'];
                                    $report['modules'][$name]['description'] = $module['description'];
                                }
                                $report['errors'] = count($reportFile['errors']);
                                $reports[] = $report;
                            }
                        // }
                    }
                }
            }
        }
        return array_reverse($reports);
    }
    
    public function getReport(string $website, string $id) {
        // $security = $this->security($id);
        // if (!$security) {return $this->redirect($this->generateUrl('report'));}
        $reportFile = $this->getParameter('reports_dir').$website.'/'.$id.'.json';
        $filesystem = new Filesystem();
        $exist = $filesystem->exists($reportFile);
        if (!$exist) {return false;}
        $file = file_get_contents($reportFile);
        $report = json_decode($file, true);
        
        return $report;
    }

    private function security(string $idReport = null) {
        if (!$this->getUser()) {return false;} // If user not connected
        $reports = $this->getUser()->getReports();
        if ($idReport) { // Check report owner
            if (!in_array($idReport, $reports)) {return false;} // User have not permission
        }
        return true;
    }
}
