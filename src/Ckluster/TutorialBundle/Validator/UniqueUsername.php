<?php

namespace Ckluster\TutorialBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Unique constraint
 *
 * @author arturo
 */
class UniqueUsername extends Constraint {

    public $message = 'A registry with {{ property }} with that value already exists.';

    public function validatedBy()
    {
        return 'tutorialbundle.validator.uniqueusername';
    }

}
