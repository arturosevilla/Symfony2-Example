<?php

namespace Ckluster\TutorialBundle\Util\ShoppingCart;

use Ckluster\TutorialBundle\Entity\Product;
use Ckluster\TutorialBundle\Entity\ShoppingCartItem;
use Ckluster\TutorialBundle\Entity\User;
use Doctrine\ORM\EntityManager;

/**
 * UserShoppingCartManager
 *
 * @author arturo
 */
class UserShoppingCartManager extends AbstractShoppingCartManager {

    /**
     *
     * @var Ckluster\TutorialBundle\Entity\User
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
