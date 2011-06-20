<?php

namespace Ckluster\TutorialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ckluster\TutorialBundle\Entity\User;
use CKluster\TutorialBundle\Entity\Order;

/**
 * UserController
 *
 * @author arturo
 */
class UserController extends Controller {
    
    /**
     * @Route("/login_check", name="_security_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/", name="homeuser")
     * @Template()
     */
    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();

        $anonymous_cart = $this->get('tutorial.shopping_cart.anonymous');
        if (count($anonymous_cart->getItems()) > 0) {
            $user_cart = $this->get('tutorial.shopping_cart');
            $user_cart->addFromShoppingCart($anonymous_cart);
        }

        return array('user' => $user, 'orders' => $user->getOrders());
    }

    /**
     * @Route("/shopping_cart/buy", name="shopping_cart_buy", requirements={"_method"="POST"})
     *
     */
    public function shoppingCartBuyAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if (count($user->getShoppingCart()) === 0) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $order = $user->buyShoppingCart('0.07');

        $em = $this->get('doctrine')->getEntityManager();
        $em->flush();

        $this->sendEmailWithOrder($user, $order);

        return $this->redirect($this->generateUrl('homeuser'));
    }

    private function sendEmailWithOrder(User $user, Order $order)
    {
        $content = $this->renderView('TutorialBundle:Order:receipt.html.twig', array('user' => $user, 'order' => $order));
        $message = \Swift_Message::newInstance()
            ->setSubject('Order no. '.$order->getId().' processed')
            ->setFrom('example@server.com')
            ->setTo($user->getEmail())
            ->setBody($content, 'text/html');
        $this->get('mailer')->send($message);
    }
}
