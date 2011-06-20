<?php

namespace Ckluster\TutorialBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\NoResultException;

/**
 * OrderController
 *
 * @author arturo
 */
class OrderController extends Controller {
    
    /**
     * @Route("/receipt/{id}", name="receipt_view")
     * @Template()
     */
    public function receiptAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->get('doctrine')->getEntityManager();
        $query = $em->createQuery('SELECT o FROM TutorialBundle:Order o INNER JOIN o.user u WHERE o.id = ?1 AND u.id = ?2');
        $query->setParameter(1, $id);
        $query->setParameter(2, $user->getId());
        try {
            $order = $query->getSingleResult();
        } catch (NoResultException $exc) {
            throw new NotFoundHttpException('Order not found');
        }

        return array('order' => $order, 'user' => $user);
    }

}
