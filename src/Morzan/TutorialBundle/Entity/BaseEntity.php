<?php

namespace Morzan\TutorialBundle\Entity;

/**
 * The class from which all entities must inherit.
 *
 * @author arturo
 */
class BaseEntity {

    protected function generateID()
    {
        return \uniqid();
    }

    protected function now()
    {
        return new \DateTime('now');
    }

}
