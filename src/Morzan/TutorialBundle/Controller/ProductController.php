<?php

namespace Morzan\TutorialBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * ProductController
 *
 * @author arturo
 */
class ProductController extends Controller {

    /**
     * @Route("/view/{id}", name="product_view")
     * @Template()
     */
    public function viewAction($id)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $product = $em->find('TutorialBundle:Product', $id);

        if ($product == NULL) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $this->get('tutorial.lastviewed')->registerLastViewed($product);

        return array('product' => $product);
    }

    /**
     * @Route("/shop/{id}", name="product_shopping_cart")
     * @Template()
     */
    public function shopAction($id)
    {
        $em = $this->get('doctrine')->getEntityManager();
    }

}
