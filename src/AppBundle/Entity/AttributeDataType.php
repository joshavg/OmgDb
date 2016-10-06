<?php
namespace AppBundle\Entity;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AttributeDataType
{
    const UNKOWN = 'unknown';

    const TEXT = 'text';

    const MARKDOWN = 'markdown';

    const NUMBER = 'number';

    const LABEL = 'label';

    const BOOLEAN = 'boolean';

    const DATE = 'date';

    /**
     * @return AttributeDataType[]
     */
    public static function getTypes()
    {
        return [
            new AttributeDataType(static::TEXT, TextareaType::class),
            new AttributeDataType(static::MARKDOWN, TextareaType::class,
                                  'transformer.property.markdown'),
            new AttributeDataType(static::LABEL, TextType::class),
            new AttributeDataType(static::NUMBER, NumberType::class),
            new AttributeDataType(static::BOOLEAN, CheckboxType::class),
            new AttributeDataType(static::DATE, DateType::class, 'transformer.property.date')
        ];
    }

    public static function getByName($name)
    {
        $all = static::getTypes();
        foreach ($all as $type) {
            if ($type->getName() === $name) {
                return $type;
            }
        }

        return new AttributeDataType(static::UNKOWN, TextType::class);
    }

    private $name;

    private $fieldType;

    private $transformerName;

    private function __construct($name, $fieldType,
                                 $transfomerName = 'transformer.property.standard')
    {
        $this->name = $name;
        $this->fieldType = $fieldType;
        $this->transformerName = $transfomerName;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTransformerName()
    {
        return $this->transformerName;
    }

    public function getFieldType()
    {
        return $this->fieldType;
    }

    public function __toString()
    {
        return $this->name;
    }

}
