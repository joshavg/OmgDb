<?php
namespace AppBundle\Entity;

class AttributeDataType
{
    
    public static function getTypes()
    {
        return [
            new AttributeDataType('text'),
            new AttributeDataType('number'),
            new AttributeDataType('boolean')
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

}