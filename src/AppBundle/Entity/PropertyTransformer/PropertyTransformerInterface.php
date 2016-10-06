<?php

namespace AppBundle\Entity\PropertyTransformer;

interface PropertyTransformerInterface
{

    /**
     * @param $value
     * @return mixed
     */
    function fromDatabaseToNormalForm($value);

    /**
     * @param $value
     * @return mixed
     */
    function fromNormalFormToDatabase($value);

    /**
     * @param $value
     * @return mixed
     */
    function fromNormalFormToTemplate($value);

}
