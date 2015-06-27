<?php
namespace laniger\Neo4jBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use laniger\Neo4jBundle\Architecture\Neo4jClientConsumer;

class Neo4jCallbackConstraintValidator extends ConstraintValidator
{
    use Neo4jClientConsumer;

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($value, Constraint $constraint)
    {
        $callback = $constraint->callback;
        $valid = $callback($this->client, $value);

        if (! $valid) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
