<?php

namespace Morzan\TutorialBundle\Controller;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Morzan\TutorialBundle\Entity\User;

class DefaultController extends Controller
{

    const SHOWCASE_LIMIT = 5;
    const LASTVIEWED_EXPIRE_TIME = 63072000; //2 years
    const PROVIDER_KEY = 'main';

    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->get('doctrine')->getEntityManager();
        $query = $em->createQuery('SELECT p FROM TutorialBundle:Product p ORDER BY p.dateOfCreation DESC');
        $query->setMaxResults(self::SHOWCASE_LIMIT);
        $new_products = $query->getResult();
        
        $request = $this->get('request');
        if ($request->headers->hasCookie('lastViewed')) {
            $last_viewed_str = $request->headers->getCookie('lastViewed')->getValue();
            $viewed_ids = \array_map(strval, \preg_split(';', $last_viewed_str));

            if (count($viewed_ids) > self::SHOWCASE_LIMIT) {
                $viewed_ids = \array_splice($viewed_ids, 0, self::SHOWCASE_LIMIT);
                $cookie_value = \implode(';', $viewed_ids);
                $new_cookie = new Cookie('lastViewed', $cookie_value, \time() + self::LASTVIEWED_EXPIRE_TIME);
                $this->get('response')->headers->setCookie($new_cookie);

            }

            $query = $em->createQuery('SELECT p FROM TutorialBundle:Product p IN ?1');
            $query->setParameter(1, $viewed_ids);

            $last_viewed = $query->getResult();

        } else {
            $last_viewed = array();
        }

        return array('new_products' => $new_products, 'last_viewed' => $last_viewed);
    }

    /**
     * @Route("/login", name="_login")
     * @Template()
     */
    public function loginAction()
    {
        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }

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
     * @Route("/signup", name="signup")
     * @Template()
     */
    public function signupAction()
    {
        $user = new User();

        $form = $this->get('form.factory')->createBuilder('form', $user)
            ->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add('email', 'email')
            ->add('password', 'repeated', array('type' => 'password', 'first_name' => 'password',
                                                'second_name' => 'confirm_password'))
            ->add('address', 'text')
            ->getForm();
        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->get('doctrine')->getEntityManager();
                $em->persist($user);
                $em->flush();

                $this->authenticate($user);

                $this->redirect($this->generateUrl('homeuser'));
            }
        }

        return array('form' => $form->createView());
    }

    private function authenticate(UserInterface $user)
    {
        $token = new UsernamePasswordToken($user, null, self::PROVIDER_KEY, $user->getRoles());

        $this->get('security.context')->setToken($token);
    }
}
