<?php

namespace AppBundle\Validator\Constraint;


use AppBundle\Validator\CsvFileValidator;
use Symfony\Component\Validator\Constraint;

class CsvFile extends Constraint
{

    public $message = 'CSV required, {{ mime }} given';

    public function validatedBy()
    {
        return CsvFileValidator::class;
    }

}
