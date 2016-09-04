<?php
namespace AppBundle\Entity;

class AttributeDataType
{

    const TEXT = 'text';

    const MARKDOWN = 'markdown';

    const NUMBER = 'number';

    const LABEL = 'label';

    const BOOLEAN = 'boolean';

    /**
     * @return AttributeDataType[]
     */
    public static function getTypes()
    {
        return [
            new AttributeDataType(static::TEXT),
            new AttributeDataType(static::MARKDOWN),
            new AttributeDataType(static::LABEL),
            new AttributeDataType(static::NUMBER),
            new AttributeDataType(static::BOOLEAN)
        ];
    }

    public static function getByName($name)
    {
        // TODO make the instances somewhat singleton-ish
        return new AttributeDataType($name);
    }

    private $name;

    private function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }

}
