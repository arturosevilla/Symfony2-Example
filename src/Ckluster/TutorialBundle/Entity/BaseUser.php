<?php

namespace Ckluster\TutorialBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of BaseUser
 * @ORM\MappedSuperClass
 * 
 * @author arturo
 */
abstract class BaseUser extends BaseEntity implements UserInterface {

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     *
     * @return string
     */
    private $salt;

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
    private $firstName;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $lastName;

    public function __construct($psFirstName = null, $psLastName = null, $psPassword = null)
    {
        parent::__construct();
        $this->salt = $this->buildSalt();

        $this->firstName = $psFirstName;
        $this->lastName = $psLastName;
        $this->password = $psPassword;
    }

    const SALT_CHARACTERS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()_+=';
    const SALT_MAX_LENGTH = 20;
    const SALT_MIN_LENGTH = 15;

    private function buildSalt()
    {
        $salt_length = \mt_rand(self::SALT_MIN_LENGTH, self::SALT_MAX_LENGTH);
        $salt_char_length = \strlen(self::SALT_CHARACTERS);
        $salt_chars = self::SALT_CHARACTERS;
        $salt = '';
        for ($i = 0; $i < $salt_length; $i++) {
            $salt .= $salt_chars[\mt_rand(0, $salt_char_length - 1)];
        }

        return $salt;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($psFirstName)
    {
        if ($psFirstName === NULL) {
            throw new \InvalidArgumentException('pFirstName must not be null');
        }

        $this->firstName = $psFirstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($psLastName)
    {
        if ($psLastName === NULL) {
            throw new \InvalidArgumentException('pLastName must not be null');
        }

        $this->lastName = $psLastName;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($psPassword)
    {
        $this->password = $psPassword;
    }

    public function eraseCredentials()
    {
    }

    public function equals(UserInterface $user)
    {
        if (!(\is_a($user, \get_class($this)))) {
            return false;
        }

        return $this->getId() === $user->getId();
    }

    public function getSalt()
    {
        return $this->salt;
    }
    
}
