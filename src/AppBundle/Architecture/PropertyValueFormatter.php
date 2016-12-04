<?php

namespace AppBundle\Architecture;

use AppBundle\Entity\AttributeDataType;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyTransformer\PropertyTransformerRepository;
use Michelf\MarkdownExtra;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class PropertyValueFormatter extends \Twig_Extension
{

    /**
     * @var Translator
     */
    private $trans;

    /**
     * @var PropertyTransformerRepository
     */
    private $transformerRepo;

    public function __construct(Translator $trans, PropertyTransformerRepository $transformerRepo)
    {
        $this->trans = $trans;
        $this->transformerRepo = $transformerRepo;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('propertyvalue', [
                $this, 'formatPropertyValue'
            ]),
            new \Twig_SimpleFilter('escapeproperty', [
                $this, 'getPropertyEscape'
            ])
        ];
    }

    public function formatPropertyValue(Property $prop)
    {
        $datatype = $prop->getAttribute()->getDataType();
        $transformer = $this->transformerRepo->getTransformer($datatype->getTransformerName());

        return $transformer->fromNormalFormToTemplate($prop->getValue());
    }

    public function getPropertyEscape(Property $prop)
    {
        $datatype = $prop->getAttribute()->getDataType();
        $transformer = $this->transformerRepo->getTransformer($datatype->getTransformerName());

        return $transformer->escapeTemplate();
    }

    public function getName()
    {
        return 'filter.formatter.property.value';
    }

}
