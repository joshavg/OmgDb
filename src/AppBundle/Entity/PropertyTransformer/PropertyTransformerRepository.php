<?php

namespace AppBundle\Entity\PropertyTransformer;


class PropertyTransformerRepository
{

    /**
     * @var PropertyTransformerTrait[]
     */
    private $transformers;

    /**
     * @param $name
     * @param PropertyTransformerTrait $transformer
     */
    public function addTransformer($name, PropertyTransformerTrait $transformer)
    {
        $this->transformers[$name] = $transformer;
    }

    /**
     * @param $name
     * @return PropertyTransformerTrait
     */
    public function getTransformer($name)
    {
        if (isset($this->transformers[$name])) {
            return $this->transformers[$name];
        }

        throw new \InvalidArgumentException('unknown property transformer: ' . $name);
    }

}
