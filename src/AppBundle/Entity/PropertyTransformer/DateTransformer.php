<?php

namespace AppBundle\Entity\PropertyTransformer;


use AppBundle\Entity\Repository\DateFactory;

class DateTransformer implements PropertyTransformerInterface
{

    /**
     * @var DateFactory
     */
    private $dateFactory;

    public function __construct(DateFactory $dateFactory)
    {
        $this->dateFactory = $dateFactory;
    }

    /**
     * @param $value
     * @return \DateTime
     */
    public function fromDatabaseToNormalForm($value)
    {
        return $this->dateFactory->fromString(strval($value));
    }

    /**
     * @param $value
     * @return mixed
     */
    public function fromNormalFormToDatabase($value)
    {
        return $this->dateFactory->toString($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function fromNormalFormToTemplate($value)
    {
        return $this->dateFactory->toString($value);
    }
}
