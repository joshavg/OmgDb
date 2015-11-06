<?php
namespace laniger\Neo4jBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use laniger\Neo4jBundle\Architecture\Neo4jClientConsumer;

class Neo4jUniqueLabelConstraintValidator extends ConstraintValidator
{
    use Neo4jClientConsumer;

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($value, Constraint $constraint)
    {
        if(!Neo4jLabelConstraintValidator::isValidLabel($value)) {
            return;
        }

        $res = $this->client->cypher('
            MATCH (n:' . $value . ') RETURN COUNT(n) AS cnt
        ', [
            'label' => $value
        ]);

        $cnt = $res->getRows()['cnt'][0];
        if ($cnt > 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%label%', $value)
                ->addViolation();
        }
    }
}