<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AttributeName extends Constraint
{
    public $message = 'err.attributename.unique';

    public function validatedBy()
    {
        return 'validator.attributename';
    }
    
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
