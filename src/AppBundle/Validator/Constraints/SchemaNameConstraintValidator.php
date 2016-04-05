<?php
namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\SchemaRepository;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class SchemaNameConstraintValidator extends ConstraintValidator
{
    /**
     * @var SchemaRepository
     */
    private $repo;

    public function __construct(SchemaRepository $repo)
    {
        $this->repo = $repo;
    }

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($value, Constraint $constraint)
    {
        $valid = $this->repo->isSchemaUniqueForCurrentUser($value);

        if (!$valid) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
