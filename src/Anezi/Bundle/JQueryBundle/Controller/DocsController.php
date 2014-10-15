<?php

namespace Anezi\Bundle\JQueryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DocsController extends Controller
{
    public function indexAction()
    {
        return $this->render(
            'JQueryBundle:docs:index.html.twig'
        );
    }
}
