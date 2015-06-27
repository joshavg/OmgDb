<?php
namespace laniger\Neo4jBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use laniger\Neo4jBundle\Architecture\Neo4jClientConsumer;

class Neo4jUniqueNameConstraintValidator extends ConstraintValidator
{
    use Neo4jClientConsumer;

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($value, Constraint $constraint)
    {
        $dat = $this->client->cypher('
            MATCH (n:' . $constraint->label . ')
            WHERE n.name = {name}
            RETURN COUNT(n) AS cnt
        ', [
            'name' => $value
        ]);

        if ($dat[0]['cnt'] > 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%value%', $value)
                ->setParameter('%label%', $constraint->label)
                ->addViolation();
        }
    }
}
