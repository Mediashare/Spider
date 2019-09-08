<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebSpiderController extends AbstractController
{
    /**
     * @Route("/webspider", name="webspider")
     */
    public function index() {;
        return $this->render('webspider/index.html.twig');
    }

    /**
     * @Route("/webspider/run", name="webspider_run")
     */
    public function run(Request $request) {
        $form = $request->request;
        $arguments = $this->getArguments($form, $id = uniqid());
        $website = parse_url($form->get('url'), PHP_URL_HOST);
        
        return $this->render('webspider/run.html.twig', [
            'arguments' => $arguments,
            'website' => $website,
            'id' => $id
        ]);
    }

    private function getArguments($form, $id) {
        $arguments = $form->get('url');
        $arguments .= " --id " . $id;
        if ($form->get('webspider')) {$arguments .= " -w ";}
        // $arguments .= " --quiet";
        if ($form->get('pathException')) {
            foreach ($form->get('pathException') as $pathException) {
                if ($pathException) {
                    $arguments .= " --exception " . $pathException;
                }
            }    
        }
        if ($form->get('pathRequired')) {
            foreach ($form->get('pathRequired') as $pathRequire) {
                if ($pathRequire) {
                    $arguments .= " --require " . $pathRequire;
                }
            }
        }
        if ($form->get('module')) {
            foreach ($form->get('module') as $name => $module) {
                if ($module) {
                    $arguments .= " --modules " . $name;
                }
            }
        }
        
        if ($form->get('variablesInjection')) {
            foreach ((array) $form->get('variablesInjection') as $module => $variables) {
                foreach ((array) $variables['values'] as $key => $value) {
                    if ($value) {
                        $valueKey = false;
                        if (isset($variables['keys'][$key])) {$valueKey = $variables['keys'][$key];}
                        if ($valueKey) {
                            $variablesInjection[$module][$valueKey][] = $value;
                        } else {
                            $variablesInjection[$module][] = $value;
                        }
                    }
                }
            }
            if (isset($variablesInjection) && $variablesInjection) {
                $arguments .= " --inject-variable " . json_encode($variablesInjection);
            }
        }
        return $arguments;
    }
}
