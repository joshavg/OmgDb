<?php
namespace AppBundle\Entity;

class AttributeDatType
{
    
    public static function getTypes()
    {
        return [
            new AttributeType('text'),
            new AttributeType('number'),
            new AttributeType('boolean')
        ];
    }

    private $name;
    
    private function __construct($name)
    {
        $this->name = $name;
    }

}