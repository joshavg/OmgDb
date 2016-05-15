<?php
namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\AttributeRepository;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class AttributeNameValidator extends ConstraintValidator
{
    /**
     * @var AttributeRepository
     */
    private $repo;

    public function __construct(AttributeRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Attribute $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $valid = $this->repo->isNameUniqueForCurrentUser($value);

        if (!$valid) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
