<?php

namespace AppBundle\Architecture;

use AppBundle\Entity\AttributeDataType;
use AppBundle\Entity\Property;
use Michelf\MarkdownExtra;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class PropertyValueFormatter extends \Twig_Extension
{

    /**
     * @var Translator
     */
    private $trans;

    public function __construct(Translator $trans)
    {
        $this->trans = $trans;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('propertyvalue', [
                $this, 'formatPropertyValue'
            ])
        ];
    }

    public function formatPropertyValue(Property $prop)
    {
        switch ($prop->getAttribute()->getDataType()) {
            case AttributeDataType::BOOLEAN:
                $label = $prop->getValue() ? 'label.property.value.true' :
                    'label.property.value.false';
                return $this->trans->trans($trans);
            case AttributeDataType::NUMBER:
            case AttributeDataType::TEXT:
                return $prop->getValue();
            case AttributeDataType::MARKDOWN:
                return MarkdownExtra::defaultTransform($prop->getValue());
        }
        return $prop->getValue();
    }

    public function getName()
    {
        return 'filter.formatter.property.value';
    }

}
