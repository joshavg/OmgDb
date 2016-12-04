<?php

namespace AppBundle\Entity\PropertyTransformer;

trait PropertyTransformerTrait
{

    /**
     * @param $value
     * @return mixed
     */
    public abstract function fromDatabaseToNormalForm($value);

    /**
     * @param $value
     * @return mixed
     */
    public abstract function fromNormalFormToDatabase($value);

    /**
     * @param $value
     * @return mixed
     */
    public abstract function fromNormalFormToTemplate($value);

    /**
     * @return boolean
     */
    public function escapeTemplate() {
        return true;
    }

}
