<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class JsonController extends AbstractController
{
    /**
     * @Route("/json", name="json")
     */
    public function index(Request $request) {
        $data = $request->request->get('data');
        $data = json_decode($data, true);
        
        return $this->json($data);
    }
}
