<?php

namespace AppBundle\Validator;


use AppBundle\Service\CsvParser;
use AppBundle\Validator\Constraint\CsvFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CsvFileValidator extends ConstraintValidator
{

    /**
     * @var CsvParser
     */
    private $parser;

    public function __construct(CsvParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param UploadedFile $value
     * @param CsvFile|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value->getMimeType() !== 'text/plain') {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ mime }}', $value->getMimeType())
                ->addViolation();
        } else {
            $rows = $this->parser->parseFile($value->getPathname());
            if (count($rows) === 0) {
                $this->context
                    ->buildViolation('File seems to be invalid or empty')
                    ->addViolation();
            }
        }
    }

}
