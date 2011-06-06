<?php

namespace Morzan\TutorialBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * An item for an order.
 *
 * @ORM\Entity
 *
 * @author arturo
 */
class OrderItem {

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="items")
     *
     * @Assert\NotBlank()
     *
     * @var Morzan\TutorialBundle\Entity\Order
     */
    private $order;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product")
     *
     * @Assert\NotBlank()
     *
     * @var Morzan\TutorialBundle\Entity\Product
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank()
     * @Assert\Min(limit=1)
     *
     * @var integer
     */
    private $quantity;

    public function __construct(Order $pOrder, Product $pProduct, int $pQuantity)
    {
        $this->order = $pOrder;
        $this->product = $pProduct;
        $this->quantity = $pQuantity;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder(Order $pOrder)
    {
        $this->order = $pOrder;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct(Product $pProduct)
    {
        $this->product = $pProduct;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity(int $pQuantity)
    {
        $this->quantity = $pQuantity;
    }

}
