<?php

namespace Ckluster\TutorialBundle\Util;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Ckluster\TutorialBundle\Entity\Product;

/**
 *
 * @author arturo
 */
interface LastViewedManagerInterface {

    /**
     * Get the last "$limit" products viewed
     */
    function getLastViewed();

    /**
     * Registers the last product viewed
     */
    function registerLastViewed(Product $product);

    /**
     * Returns the maximum number of products in the showcase
     */
    function getMaxInShowcase();

}
