<?php
namespace laniger\Neo4jBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Neo4jLabelConstraint extends Constraint
{

    public $message = '"%label%" is not a valid label name';

    public function validatedBy()
    {
        return 'neo4j.validator.label';
    }
}