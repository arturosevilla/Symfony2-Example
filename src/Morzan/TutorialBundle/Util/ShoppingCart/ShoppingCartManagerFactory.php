<?php

namespace Morzan\TutorialBundle\Util\ShoppingCart;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Morzan\TutorialBundle\Util\CookieVariationRegistryInterface;
use Symfony\Bundle\DoctrineBundle\Registry;

/**
 * 
 *
 * @author arturo
 */
class ShoppingCartManagerFactory {

    public function get(SecurityContextInterface $securityContext,
                        Request $request,
                        Registry $doctrine,
                        CookieVariationRegistryInterface $cookieRegistry,
                        $forceAnonymous = false)
    {
        $em = $doctrine->getEntityManager();
        if ($securityContext->isGranted('ROLE_USER') && !$forceAnonymous) {
            return new UserShoppingCartManager($securityContext->getToken()->getUser(), $em);
        } else {
            return new AnonymousShoppingCartManager($request, $em, $cookieRegistry);
        }
    }



}
