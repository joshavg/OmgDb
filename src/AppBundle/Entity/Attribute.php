<?php
namespace AppBundle\Entity;

class Attribute
{

    private $name;

    private $createdAt;
    
    private $dataType;
    
    private $schema;

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
        $this->dataType = $type;
        return $this;
    }
    
    public function getDataType()
    {
        return $this->dataType;
    }
    
    public function setSchema(Schema $schema)
    {
        $this->schema = $schema;
        return $this;
    }
    
    public function getSchema()
    {
        return $this->schema;
    }
}
