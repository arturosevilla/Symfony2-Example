<?php

namespace Morzan\TutorialBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The user class
 *
 * @ORM\Entity
 *
 * @author arturo
 */
class User extends BaseEntity implements UserInterface {

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $firstName;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $lastName;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     * @Assert\Email()
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
    private $password;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     * 
     * @var string
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="user")
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity="ShoppingCartItem", mappedBy="user")
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    private $cartItems;

    public function __construct(string $pFirstName, string $pLastName,
                                string $pEmail, string $pPassword,
                                string $pAddress)
    {
        $this->id = $this->generateID();
        $this->firstName = $pFirstName;
        $this->lastName = $pLastName;
        $this->email = $pEmail;
        $this->address = $pAddress;
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

    public function buyShoppingCart(string $pTaxPercentage)
    {
        $order = new Order($this, $pTaxPercentage, $this->cartItems);
        $this->orders[] = $order;

        $this->cartItems->clear();
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName(string $pFirstName)
    {
        if ($pFirstName === NULL) {
            throw new \InvalidArgumentException('pFirstName must not be null');
        }
        
        $this->firstName = $psFirstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName(string $pLastName)
    {
        if ($pLastName === NULL) {
            throw new \InvalidArgumentException('pLastName must not be null');
        }

        $this->lastName = $pLastName;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $pEmail)
    {
        $this->email = $pEmail;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress(string $pAddress)
    {
        $this->address = $pAddress;
    }

    public function equals(UserInterface $user)
    {
        if (!($user instanceof User)) {
            return false;
        }

        return $this->id === $user->id;
    }

    public function getRoles()
    {
        return array();
    }

    public function getSalt()
    {
        return '';
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $pPassword)
    {
        $this->password = $pPassword;
    }

}
