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
        $className = $this->context->getCurrentClass();
        $property = $this->context->getCurrentProperty();

        $repository = $this->em->getRepository($className);
        if ($repository === null) {
            throw new \InvalidArgumentException(\sprintf('No repository for "%s" was found.', $className));
        }
        
        $metadata = $this->em->getClassMetadata($className);

        if (!$metadata->hasField($property)) {
            throw new \InvalidArgumentException(\sprintf('The metadata for "%s" does not contain the field "%s".', $className, $property));
        }

        $users = $repository->findBy(array($property => $value));
        $valid = \count($users) === 0;
        if (!$valid) {
            $this->setMessage($constraint->message, array('{{ property }}' => $property));
        }

        return $valid;
    }
}
