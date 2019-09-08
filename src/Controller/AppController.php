<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Module;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app")
     */
    public function index() {
        $module = new Module();
        $modules = $module->getModules();
        return $this->render('app/index.html.twig', [
            'modules' => $modules
        ]);
    }
}
