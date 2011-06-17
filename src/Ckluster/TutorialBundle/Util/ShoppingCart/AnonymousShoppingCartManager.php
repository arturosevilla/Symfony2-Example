<?php

namespace Ckluster\TutorialBundle\Util\ShoppingCart;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Ckluster\TutorialBundle\Entity\Product;
use Ckluster\TutorialBundle\Entity\User;
use Ckluster\TutorialBundle\Entity\ShoppingCartItem;
use Ckluster\TutorialBundle\Util\CookieVariationRegistryInterface;

/**
 * AnonymousShoppingCartManager
 *
 * @author arturo
 */
class AnonymousShoppingCartManager extends AbstractShoppingCartManager {

    /**
     *
     * @var Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     *
     * @var Ckluster\TutorialBundle\Util\CookieVariationRegistryInterface
     */
    private $cookieRegistry;

    /**
     *  @var array
     */
    private $items;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    const COOKIE_NAME = 'shoppingCart';
    const COOKIE_EXPIRE = 63072000;

    public function __construct(Request $request, 
                                EntityManager $em,
                                CookieVariationRegistryInterface $cookieRegistry)
    {
        $this->request = $request;
        $this->cookieRegistry = $cookieRegistry;
        $this->em = $em;
        $this->items = $this->getItemsFromCookie();
    }

    private function getItemsFromCookie()
    {
        if (!$this->request->cookies->has(self::COOKIE_NAME)) {
            return array();
        }

        $items_str = \base64_decode($this->request->cookies->get(self::COOKIE_NAME));
        $items_map = \array_map('\trim', \explode('|', $items_str));
        $length_items = count($items_map);

        if ($length_items === 0) {
            return array();
        }

        $inConditions = array();

        for ($i = 1; $i <= $length_items; $i++) {
            $inConditions[] = "?$i";
        }

        $in = \implode(', ', $inConditions);

        $query = $this->em->createQuery("SELECT p FROM TutorialBundle:Product p WHERE p.id IN ($in)");

        $quantities = array();
        for ($i = 1; $i <= $length_items; $i++) {
            $props = \array_map('\trim', \explode(',', $items_map[$i - 1]));
            $product_id = $props[0];
            $quantity = intval($props[1]);
            $query->setParameter($i, $product_id);
            $quantities[$product_id] = $quantity;
        }

        $products = $query->getResult();

        $items = array();
        foreach ($products as $product) {
            $items[] = new ShoppingCartItem(new User(), $product, $quantities[$product->getId()]);
        }

        return $items;
    }

    private function getCookieFromItems()
    {
        $props = array();
        foreach ($this->items as $item) {
            $props[] = $item->getProduct()->getId() . ',' . \strval($item->getQuantity());
        }

        return \base64_encode(\implode('|', $props));
    }

    public function addItem(Product $product, $quantity)
    {
        if ($quantity <= 0) {
            return;
        }
        
        $found = false;
        foreach ($this->items as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $item->setQuantity($item->getQuantity() + $quantity);
                $found = true;
            }
        }

        if (!$found) {
            $this->items[] = new ShoppingCartItem(new User(), $product, $quantity);
        }
        
        $cookie_value = $this->getCookieFromItems();
        $this->cookieRegistry->setCookie(self::COOKIE_NAME, $cookie_value, self::COOKIE_EXPIRE);
    }

    public function getItems()
    {
        return $this->items;
    }

    public function clearItems()
    {
        $this->items = array();
        $this->cookieRegistry->expireCookie(self::COOKIE_NAME);
    }

}
