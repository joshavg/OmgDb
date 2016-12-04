<?php

namespace AppBundle\Entity\PropertyTransformer;


class StandardPropertyTransformer
{
    use PropertyTransformerTrait;

    /**
     * @param $value
     * @return mixed
     */
    public function fromDatabaseToNormalForm($value)
    {
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function fromNormalFormToDatabase($value)
    {
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function fromNormalFormToTemplate($value)
    {
        return $value;
    }
}
