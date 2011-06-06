<?php

namespace Morzan\TutorialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TutorialBundle:Default:index.html.twig');
    }
}
