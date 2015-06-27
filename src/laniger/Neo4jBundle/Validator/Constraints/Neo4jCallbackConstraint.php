<?php
namespace laniger\Neo4jBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Neo4jCallbackConstraint extends Constraint
{

    public $message;

    public $callback;

    public function __construct($message, callable $callback)
    {
        $this->message = $message;
    }

    public function validatedBy()
    {
        return 'neo4j.validator.callback';
    }
}