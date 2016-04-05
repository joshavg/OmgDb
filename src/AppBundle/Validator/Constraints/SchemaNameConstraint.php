<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class SchemaNameConstraint extends Constraint
{
    public $message = 'err.schemaname.unique';

    public function validatedBy()
    {
        return 'validator.schemaname';
    }
}
