<?php

namespace Morzan\TutorialBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;

class DefaultController extends Controller
{

    const SHOWCASE_LIMIT = 5;
    const LASTVIEWED_EXPIRE_TIME = 63072000; //2 years

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
}
