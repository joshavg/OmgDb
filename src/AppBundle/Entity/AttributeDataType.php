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
            new AttributeDataType(static::MARKDOWN, TextareaType::class),
            new AttributeDataType(static::LABEL, TextType::class),
            new AttributeDataType(static::NUMBER, NumberType::class),
            new AttributeDataType(static::BOOLEAN, CheckboxType::class),
            new AttributeDataType(static::DATE, DateType::class)
        ];
    }

    public static function getByName($name)
    {
        $all = static::getTypes();
        foreach($all as $type) {
            if($type->getName() === $name) {
                return $type;
            }
        }

        return new AttributeDataType(static::UNKOWN, TextType::class);
    }

    private $name;

    private $fieldType;

    private function __construct($name, $fieldType)
    {
        $this->name = $name;
        $this->fieldType = $fieldType;
    }

    public function getName()
    {
        return $this->name;
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
