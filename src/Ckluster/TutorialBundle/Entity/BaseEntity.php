<?php

namespace Ckluster\TutorialBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * The class from which all entities must inherit.
 * @ORM\MappedSuperClass
 * @author arturo
 */
class BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $id;

    protected function __construct()
    {
        $this->id = $this->generateID();
    }

    protected function generateID()
    {
        return \uniqid();
    }

    protected function now()
    {
        return new \DateTime('now');
    }

    public function getId()
    {
        return $this->id;
    }

}
