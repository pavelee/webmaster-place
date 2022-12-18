<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TemplateController extends AbstractController
{
    #[Route('/{name}', name: 'app_template')]
    public function index(Request $request): Response
    {
        $templateName = $request->get('name');
        return $this->render(sprintf('%s/index.html.twig', $templateName), []);
    }
}
