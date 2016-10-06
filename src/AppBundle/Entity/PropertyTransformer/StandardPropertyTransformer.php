<?php

namespace AppBundle\Entity\PropertyTransformer;


class StandardPropertyTransformer implements PropertyTransformerInterface
{

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
