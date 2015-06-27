<?php
namespace laniger\Neo4jBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Neo4jUniqueNameConstraint extends Constraint
{

    public $message = '%value% is no unique name on label %label%';

    public $label;

    public function __construct($label)
    {
        $this->label = $label;
    }

    public function validatedBy()
    {
        return 'neo4j.validator.uniquename';
    }
}