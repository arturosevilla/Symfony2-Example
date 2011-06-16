<?php

namespace Morzan\TutorialBundle\Util;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\CoreEvents;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * CookieVariationRegistry
 *
 * @author arturo
 */
class CookieVariationRegistry implements CookieVariationRegistryInterface {

    /**
     *
     * @var array
     */
    private $cookies;
    
    /**
     *
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;
    
    /**
     *
     * @var bool
     */
    private $registered;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->cookies = array();
        $this->registered = false;
    }

    public function setCookie($name, $value, $expire)
    {
        $this->registerMyself();
        
        foreach ($this->cookies as $key => $cookie) {
            if ($cookie->getName() === $name) {
                unset($this->cookies[$key]);
            }
        }

        $this->cookies[] = new Cookie($name, $value, \time() + $expire);
    }

    public function onCoreResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        foreach ($this->cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }
    }

    private function registerMyself()
    {
        if ($this->registered) {
            return;
        }

        $this->dispatcher->addListener(CoreEvents::RESPONSE, array($this, 'onCoreResponse'));
        $this->registered = true;
    }

}
