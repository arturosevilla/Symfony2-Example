<?php

namespace Morzan\TutorialBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Morzan\TutorialBundle\Entity\Product;
use Morzan\TutorialBundle\Entity\ProductReview;

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
            throw new NotFoundHttpException('Product not found');
        }

        $canReview = $this->canCreateReview($product);

        $this->get('tutorial.lastviewed')->registerLastViewed($product);
        return array('product' => $product, 'canReview' => $canReview);
    }

    /**
     * @Route("/review/{id}", name="product_review")
     * @Template()
     */
    public function createReviewAction($id)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $product = $em->find('TutorialBundle:Product', $id);

        if ($product == NULL || !$this->canCreateReview($product)) {
            throw new NotFoundHttpException('Product not found');
        }

        $user = $this->get('security.context')->getToken()->getUser();

        $productReview = new ProductReview();

        $form = $this->get('form.factory')->createBuilder('form', $productReview)
            ->add('review', 'textarea')
            ->add('rating', 'choice', array(
                'choices' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5'
            )))
            ->getForm();

        $productReview->setProduct($product);
        $productReview->setAuthor($user);

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $em->merge($product);
                $product->addReview($productReview);
                $em->flush();

                return $this->redirect($this->generateUrl('product_view', array('id' => $product->getId())));
            }
        }

        return array('form' => $form->createView(), 'product' => $product);
    }



    /**
     * @Route("/shop/{id}", name="product_shopping_cart")
     * @Template()
     */
    public function shopAction($id)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $product = $em->find('TutorialBundle:Product', $id);
        if ($product === null) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $shopping_cart = $this->get('tutorial.shopping_cart');
        $shopping_cart->addItem($product, 1);

        return $this->redirect($this->generateUrl('shopping_cart'));
    }

    private function canCreateReview(Product $product)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            return false;
        }

        $user = $this->get('security.context')->getToken()->getUser();

        foreach ($product->getReviews() as $review) {
            if ($review->getAuthor()->getId() == $user->getId()) {
                return false;
            }
        }

        return true;
    }

}
