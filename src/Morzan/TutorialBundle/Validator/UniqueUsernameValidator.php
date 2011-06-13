<?php

namespace Morzan\TutorialBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

/**
 * Validates the unique username constraint
 *
 * @author arturo
 */
class UniqueUsernameValidator extends ConstraintValidator {

    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function isValid($value, Constraint $constraint)
    {
        $repository = $this->em->getRepository($constraint->class);
        if ($repository === null) {
            throw new \InvalidArgumentException(\sprintf('No repository for "%s" was found.', $constraint->class));
        }
        
        $metadata = $this->em->getClassMetadata($constraint->class);

        if (!$metadata->hasField($constraint->property)) {
            throw new \InvalidArgumentException(\sprintf('The metadata for "%s" does not contain the field "%s".', $constraint->class, $constraint->property));
        }

        $users = $repository->findBy(array($constraint->property => $value));
        $valid = \count($users) === 0;
        if (!$valid) {
            $this->setMessage($constraint->message, array('{{ property }}' => $constraint->property));
        }

        return $valid;
    }
}
