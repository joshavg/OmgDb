<?php
namespace AppBundle\Entity;

class AttributeDataType
{

    public static $TEXT = 'text';

    public static $MARKDOWN = 'markdown';

    public static $NUMBER = 'number';

    public static $LABEL = 'label';

    public static $BOOLEAN = 'boolean';

    /**
     * @return AttributeDataType[]
     */
    public static function getTypes()
    {
        return [
            new AttributeDataType(static::$TEXT),
            new AttributeDataType(static::$MARKDOWN),
            new AttributeDataType(static::$LABEL),
            new AttributeDataType(static::$NUMBER),
            new AttributeDataType(static::$BOOLEAN)
        ];
    }

    public static function getByName($name)
    {
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
