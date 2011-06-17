<?php

namespace Morzan\TutorialBundle\Util\ShoppingCart;

use Morzan\TutorialBundle\Entity\Product;
use Morzan\TutorialBundle\Entity\ShoppingCartItem;
use Morzan\TutorialBundle\Entity\User;
use Doctrine\ORM\EntityManager;

/**
 * UserShoppingCartManager
 *
 * @author arturo
 */
class UserShoppingCartManager extends AbstractShoppingCartManager {

    /**
     *
     * @var Morzan\TutorialBundle\Entity\User
     */
    private $user;

    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(User $user, EntityManager $em)
    {
        $this->user = $user;
        $this->em = $em;
    }

    public function addItem(Product $product, $quantity)
    {
        $this->user->addItemToShoppingCart($product, $quantity);
        $this->em->flush();
    }

    public function getItems()
    {
        return $this->user->getShoppingCart();
    }

    public function clearItems()
    {
        $items = $this->getItems();
        foreach ($items as $item) {
            $this->em->remove($item);
        }
        $this->em->flush();
    }

}
