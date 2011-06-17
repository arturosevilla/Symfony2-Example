<?php

namespace Ckluster\TutorialBundle\Util;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Ckluster\TutorialBundle\Entity\Product;
use Ckluster\TutorialBundle\Entity\User;

/**
 * LastViewedHelper
 *
 * @author arturo
 */
class LastViewedManager implements LastViewedManagerInterface {

    private $em;
    private $limit;
    private $cookie_name;
    private $expire_time;
    private $request;
    private $cookieRegistry;

    public function __construct($limit, $cookie_name, $expire_time)
    {
        $this->limit = (int)$limit;
        $this->cookie_name = $cookie_name;
        $this->expire_time = (int)$expire_time;
    }

    public function setEntityManager(Registry $doctrine)
    {
        $this->em = $doctrine->getEntityManager();
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function setCookieRegistry(CookieVariationRegistryInterface $cookieRegistry)
    {
        $this->cookieRegistry = $cookieRegistry;
    }

    public function getMaxInShowcase()
    {
        return $this->limit;
    }

    public function cookieExists()
    {
        return $this->request->cookies->has($this->cookie_name);
    }
    
    public function getLastViewed()
    {
        $request = $this->request;
        
        if ($this->cookieExists()) {
            $last_viewed_str = $request->cookies->get($this->cookie_name);
            $viewed_ids = \array_map('\trim', \explode('|', $last_viewed_str));

            if (count($viewed_ids) > $this->limit) {
                $viewed_ids = \array_splice($viewed_ids, 0, $this->limit);
                $cookie_value = \implode('|', $viewed_ids);
                $this->setResponseCookie($cookie_value);
            } else {
                /* just refresh the expire time */
                $this->setResponseCookie($last_viewed_str);
            }

            $length_ids = count($viewed_ids);
            if ($length_ids === 0) {
                return array();
            }

            $params_arr = array();
            for ($i = 1; $i <= $length_ids; $i++) {
                $params_arr[] = '?' . strval($i);
            }
            $param = \implode(',', $params_arr);

            $query = $this->em->createQuery("SELECT p FROM TutorialBundle:Product p WHERE p.id IN ($param)");
            for ($i = 1; $i <= $length_ids; $i++) {
                $query->setParameter($i, $viewed_ids[$i - 1]);
            }

            $last_viewed = $query->getResult();

        } else {
            $last_viewed = array();
        }

        return $last_viewed;
    }

    public function registerLastViewed(Product $product)
    {
        $request = $this->request;
        if ($this->cookieExists()) {
            $last_viewed_str = $request->cookies->get($this->cookie_name);
            $viewed_ids = \array_map('\trim', \explode('|', $last_viewed_str));
        } else {
            $viewed_ids = array();
        }


        $product_id = $product->getId();

        /* insert at the beginning of the array */
        \array_unshift($viewed_ids, $product_id);

        $length_ids = count($viewed_ids);

        for ($i = 1; $i < $length_ids; $i++) {
            if ($viewed_ids[$i] === $product_id) {
                $length_ids--;
                unset($viewed_ids[$i]);
                if ($length_ids == 1) {
                    break;
                }
            }
        }

        if (count($viewed_ids) > $this->limit) {
            $viewed_ids = \array_splice($viewed_ids, 0, $this->limit);
        }
        
        $this->setResponseCookie($viewed_ids);
    }

    private function setResponseCookie($value)
    {
        if (\is_array($value)) {
            $value = \implode('|', $value);
        }

        $this->cookieRegistry->setCookie($this->cookie_name, $value, $this->expire_time);
    }

}
