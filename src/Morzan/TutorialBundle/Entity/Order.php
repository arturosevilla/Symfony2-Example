<?php

namespace Morzan\TutorialBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * An order.
 * @ORM\Entity
 * 
 * @author arturo
 */
class Order extends BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * 
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotBlank()
     *
     * @var DateTime
     */
    private $dateOfPurchase;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="orders")
     *
     * @Assert\NotBlank()
     *
     * @var Morzan\TutorialBundle\Entity\User
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     * @Assert\Choice({"processed", "intransit", "delivered", "notdelivered"})
     *
     * @var string
     */
    private $status;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=16)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="numeric")
     * 
     * @var string
     */
    private $subtotal;

     /**
     * @ORM\Column(type="decimal", precision=2, scale=16)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="numeric")
     *
     * @var string
     */
    private $taxPercentage;

    /**
     * @ORM\OneToMany(targetEntity="OrderItem", mappedBy="order")
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     */

    private $items;

    public function __construct(User $pUser, string $pTaxPercentage, $paItems)
    {
        $this->id = $this->generateID();
        $this->dateOfPurchase = $this->now();
        $this->user = $pUser;
        $this->status = 'processed';
        $this->subtotal = '0';
        $this->taxPercentage = $pTaxPercentage;
        $this->items = new ArrayCollection();
        
        foreach ($paItems as $item) {
            $this->addItem($paItems);
        }
    }

    public function addItem($poItem)
    {
        if ($poItem instanceof OrderItem) {
            $this->addOrderItem($poItem);
        } else {
            $orderItem = new OrderItem($this, $poItem->getProduct(),
                                       $poItem->getQuantity());
            $this->addOrderItem($orderItem);
        }
    }

    private function addOrderItem(OrderItem $item)
    {
        if ($item->getOrder()->id !== $this->id) {
            return;
        }
        
        $product = $item->getProduct();
        $cost = \bcmul($product->getCost(), strval($item->getQuantity()));
        $this->subtotal = \bcadd($this->subtotal, $cost);

        foreach ($this->items as $presentItem) {
            if ($presentItem->getProduct()->id === $item->getProduct()->id) {
                $newQuantity = $presentItem->getQuantity() + $item->getQuantity();
                $presentItem->setQuantity($newQuantity);
                return;
            }
        }

        $this->items[] = $item;
    }

    public function getDateOfPurchase()
    {
        return $this->dateOfPurchase;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $pUser)
    {
        $this->user = $pUser;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setErrorInDelivery()
    {
        $this->status = 'notdelivered';
    }

    public function setNextStageInDelivery()
    {
        switch ($this->status) {
            case 'processed':
                $this->status = 'intransit';
                break;
            case 'intransit':
                $this->status = 'delivered';
                break;
        }
    }

    public function getSubtotal()
    {
        return $this->subtotal;
    }

    public function getTotal()
    {
        return \bcadd($this->subtotal, $this->getTax());
    }

    public function getTaxPercentage()
    {
        return $this->taxPercentage;
    }

    public function setTaxPercentage(string $pTaxPercentage)
    {
        $this->taxPercentage = $pTaxPercentage;
    }

    public function getTax()
    {
        return \bcmul($this->subtotal, $this->taxPercentage);
    }

}
