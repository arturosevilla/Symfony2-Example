<?php

namespace Morzan\TutorialBundle\Util;

/**
 *
 * @author arturo
 */
interface CookieVariationRegistryInterface {

    function setCookie($name, $value, $expire);

    function expireCookie($name);

}
