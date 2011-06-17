<?php

namespace Ckluster\TutorialBundle\Util\ShoppingCart;

use Ckluster\TutorialBundle\Entity\Product;

/**
 * AbstractShoppingCartManager
 *
 * @author arturo
 */
abstract class AbstractShoppingCartManager {

    public abstract function addItem(Product $product, $quantity);
    public abstract function getItems();
    public abstract function clearItems();
    
    public function addFromShoppingCart(AbstractShoppingCartManager $manager)
    {
        $items = $manager->getItems();
        foreach ($items as $item) {
            $this->addItem($item->getProduct(), $item->getQuantity());
        }

        $manager->clearItems();
    }


}
