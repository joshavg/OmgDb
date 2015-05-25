<?php
namespace laniger\Neo4jBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use laniger\Neo4jBundle\Architecture\Neo4jClientConsumer;

class Neo4jLabelConstraintValidator extends ConstraintValidator
{
    use Neo4jClientConsumer;

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($value, Constraint $constraint)
    {
        if (!static::isValidLabel($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%label%', $value)
                ->addViolation();
        }
    }

    public static function isValidLabel($lbl) {
        return preg_match('/^[a-z]{1}[a-z0-9_]*$/i', $lbl) === 1;
    }
}