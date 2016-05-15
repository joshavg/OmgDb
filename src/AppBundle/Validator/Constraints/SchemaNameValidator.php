<?php
namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Schema;
use AppBundle\Entity\SchemaRepository;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class SchemaNameValidator extends ConstraintValidator
{
    /**
     * @var SchemaRepository
     */
    private $repo;

    public function __construct(SchemaRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Schema $value
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
