<?php

namespace AppBundle\Entity\PropertyTransformer;


use Michelf\MarkdownExtra;

class MarkdownTransformer
{
    use PropertyTransformerTrait;

    /**
     * @param $value
     * @return mixed
     */
    function fromDatabaseToNormalForm($value)
    {
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    function fromNormalFormToDatabase($value)
    {
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    function fromNormalFormToTemplate($value)
    {
        $parser = new MarkdownExtra();
        return $parser->transform($value);
    }

    public function escapeTemplate()
    {
        return false;
    }
}
