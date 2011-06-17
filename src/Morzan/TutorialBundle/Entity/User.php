<?php

namespace Morzan\TutorialBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Morzan\TutorialBundle\Validator as MyAssert;

/**
 * The user class
 *
 * @ORM\Entity
 *
 * @author arturo
 */
class User extends BaseUser implements \Serializable {

    /**
     * @ORM\Column(type="string", unique=true)
     * 
     * @Assert\NotBlank()
     * @Assert\Email()
     * @MyAssert\UniqueUsername()
     *
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     * 
     * @var string
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="user", cascade={"all"})
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity="ShoppingCartItem", mappedBy="user", cascade={"all"})
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    private $cartItems;

    public function __construct($psFirstName = null, $psLastName = null,
                                $psEmail = null, $psPassword = null,
                                $psAddress = null)
    {
        parent::__construct($psFirstName, $psLastName, $psPassword);
        
        $this->email = $psEmail;
        $this->address = $psAddress;
        $this->orders = new ArrayCollection();
        $this->cartItems = new ArrayCollection();
    }

    public function addItemToShoppingCart(Product $product, $quantity)
    {
        if ($quantity <= 0) {
            return;
        }
        
        foreach ($this->cartItems as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $item->setQuantity($item->getQuantity() + $quantity);
                return;
            }
        }

        $cartItem = new ShoppingCartItem($this, $product, $quantity);

        $this->cartItems[] = $cartItem;
    }

    public function buyShoppingCart($psTaxPercentage)
    {
        $order = new Order($this, $psTaxPercentage, $this->cartItems);
        $this->orders[] = $order;

        $this->clearShoppingCart();
    }

    public function clearShoppingCart()
    {
        $this->cartItems->clear();
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($psEmail)
    {
        $this->email = $psEmail;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($psAddress)
    {
        $this->address = $psAddress;
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public function getShoppingCart()
    {
        return $this->cartItems;
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function serialize()
    {
        return serialize($this->email);
    }
    
    public function unserialize($data)
    {
        $this->email = unserialize($data);
    }

}
