<?php

namespace Morzan\TutorialBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Morzan\TutorialBundle\Entity\Product;


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
     * @Route("/logout", name="admin_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/orders/", name="orders_catalog")
     * @Template()
     */
    public function ordersAction()
    {
        $em = $this->get('doctrine')->getEntityManager();
        $orders = $em->getRepository('TutorialBundle:Order')->findAll();
        return array('orders' => $orders);
    }

    /**
     * @Route("/orders/next/{id}", name="orders_next_stage", defaults={"_format"="json"})
     *
     */
    public function orderNextStageAction($id)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $order = $em->find('TutorialBundle:Order', $id);
        if ($order === null) {
            return new Response('');
        }

        $order->setNextStageInDelivery();
        $status = $order->getStatus();

        $em->flush();

        return new Response(\json_encode($status));
    }

    /**
     * @Route("/orders/error/{id}", name="orders_error_stage", defaults={"_format"="json"})
     *
     */
    public function orderErrorAction($id)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $order = $em->find('TutorialBundle:Order', $id);
        if ($order === null) {
            return new Response('');
        }

        $status = $order->getStatus();

        if ($status != 'delivered') {
            $order->setErrorInDelivery();
            $status = $order->getStatus();

            $em->flush();
        }

        return new Response(\json_encode($status));
    }

    /**
     * @Route("/product/", name="product_catalog")
     * @Template()
     */
    public function productAction()
    {
        $em = $this->get('doctrine')->getEntityManager();
        $query = $em->createQuery('SELECT COUNT(p.id) FROM TutorialBundle:Product p');
        $total = $query->getSingleScalarResult();
        return array('total' => $total);
    }

    /**
     * @Route("/product/paginator/{offset}/{count}", name="product_paginator", defaults={"_format"="json"})
     * @Template()
     */
    public function productPaginatorAction($offset, $count)
    {
        if ($offset < 0 || $count <= 0) {
            return array('products' => array());
        }

        $em = $this->get('doctrine')->getEntityManager();
        $query = $em->createQuery('SELECT p FROM TutorialBundle:Product p');
        $query->setFirstResult($offset);
        $query->setMaxResults($count);

        return array('products' => $query->getResult());
    }

    /**
     * @Route("/product/edit/{id}", name="product_edit", defaults={"id"=""})
     * @Template()
     */
    public function productEditAction($id)
    {
        if ($id !== '') {
            $em = $this->get('doctrine')->getEntityManager();

            $product = $em->find('TutorialBundle:Product', $id);
        } else {
            $product = new Product();
        }

        $form = $this->get('form.factory')->createBuilder('form', $product)
            ->add('name', 'text')
            ->add('description', 'text')
            ->add('cost', 'money', array('currency' => 'USD'))
            ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);
            
            if ($form->isValid()) {

                if ($id === '') {
                    $em = $this->get('doctrine')->getEntityManager();
                    $em->persist($product);
                }

                $em->flush();
                return $this->redirect($this->generateUrl('product_catalog'));
            }
        }

        return array('form' => $form->createView(), 'id' => $id);

    }
}
