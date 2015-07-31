<?php
namespace AppBundle\Entity;

class Attribute
{

    private $name;

    private $createdAt;
    
    private $dataType;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    public function setDataType(AttributeDataType $type)
    {
        $this->type = $type;
        return $this;
    }
    
    public function getDataType()
    {
        return $this->type;
    }
}