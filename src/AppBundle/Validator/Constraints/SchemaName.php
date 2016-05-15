<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SchemaName extends Constraint
{
    public $message = 'err.schemaname.unique';

    public function validatedBy()
    {
        return 'validator.schemaname';
    }
}
