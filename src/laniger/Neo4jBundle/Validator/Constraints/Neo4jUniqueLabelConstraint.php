<?php
namespace laniger\Neo4jBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Neo4jUniqueLabelConstraint extends Constraint
{

    public $message = 'The label %label% already exists';

    public function validatedBy()
    {
        return 'neo4j.validator.uniquelabel';
    }
}