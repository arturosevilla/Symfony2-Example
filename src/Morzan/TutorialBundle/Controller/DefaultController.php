<?php

namespace Morzan\TutorialBundle\Controller;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Morzan\TutorialBundle\Entity\BaseUser;
use Morzan\TutorialBundle\Entity\User;
use Morzan\TutorialBundle\Entity\AdminUser;

class DefaultController extends Controller
{

    const SHOWCASE_LIMIT = 5;
    const LASTVIEWED_EXPIRE_TIME = 63072000; //2 years
    const PROVIDER_KEY = 'user_area';

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
     * @Route("/install_admin", name="install_admin")
     */
    public function installAction()
    {
        $user = new AdminUser('Admin', 'User', 'test1234');
        $user->setUsername('admin');

        $this->encodePassword($user);

        $em = $this->get('doctrine')->getEntityManager();
        $em->persist($user);
        $em->flush();

        return new Response('success');
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
     * @Route("/verify/{username}", name="verify_username", requirements={"_method"="POST" }, defaults={"_format"="json"})
     *
     */
    public function verifyUsername($username)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $query = $em->createQuery('SELECT COUNT(u.id) FROM TutorialBundle:User u WHERE email = :email');
        $query->setParameter('email', $username);
        $count = $query->getSingleScalarResult();

        return new Response($count === 0 ? 'true' : 'false');
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

                $this->encodePassword($user);

                // persist the user
                $em = $this->get('doctrine')->getEntityManager();
                $em->persist($user);
                $em->flush();

                $this->authenticate($user);

                return $this->redirect($this->generateUrl('homeuser'));
            }
        }

        return array('form' => $form->createView());
    }

    private function encodePassword(BaseUser $user)
    {
        $encoder_factory = $this->get('security.encoder_factory');
        $encoder = $encoder_factory->getEncoder($user);
        $password_encoded = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($password_encoded);
    }

    private function authenticate(UserInterface $user)
    {
        $token = new UsernamePasswordToken($user, null, self::PROVIDER_KEY, $user->getRoles());

        $this->get('security.context')->setToken($token);
    }
}
