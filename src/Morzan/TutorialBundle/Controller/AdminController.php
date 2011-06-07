<?php

namespace Morzan\TutorialBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * AdminController
 *
 * @author arturo
 */
class AdminController extends Controller {
    
    /**
     * @Route("/", name="homeadmin")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/product/", name="product_catalog")
     * @Template()
     */
    public function productAction()
    {
        return array();
    }
}
