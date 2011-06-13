<?php

namespace Morzan\TutorialBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Unique constraint
 *
 * @author arturo
 */
class UniqueUsername extends Constraint {

    public $message = 'A registry with {{ property }} with that value already exists.';
    public $property;
    public $class;

    public function getDefaultOption()
    {
        return 'property';
    }

    public function getRequiredOptions()
    {
        return array('property', 'class');
    }

    public function validatedBy()
    {
        return 'tutorialbundle.validator.uniqueusername';
    }

}
