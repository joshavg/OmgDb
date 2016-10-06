<?php

namespace AppBundle\Entity\PropertyTransformer;


class PropertyTransformerRepository
{

    /**
     * @var PropertyTransformerInterface[]
     */
    private $transformers;

    /**
     * @param $name
     * @param PropertyTransformerInterface $transformer
     */
    public function addTransformer($name, PropertyTransformerInterface $transformer)
    {
        $this->transformers[$name] = $transformer;
    }

    /**
     * @param $name
     * @return PropertyTransformerInterface
     */
    public function getTransformer($name)
    {
        if (isset($this->transformers[$name])) {
            return $this->transformers[$name];
        }

        throw new \InvalidArgumentException('unknown property transformer: ' . $name);
    }

}
