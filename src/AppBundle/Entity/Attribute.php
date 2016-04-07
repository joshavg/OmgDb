<?php
namespace AppBundle\Entity;

/**
 * @AppBundle\Validator\Constraints\AttributeName
 */
class Attribute
{
    private $name;

    private $createdAt;

    private $dataType;

    private $schemaName;

    public function __construct()
    {
        $this->dataType = AttributeDataType::getByName(AttributeDataType::$TEXT);
    }

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

    /**
     * @param string $typename
     * @return $this
     */
    public function setDataType($typename)
    {
        if (is_string($typename)) {
            $this->dataType = AttributeDataType::getByName($typename);
        } else {
            $this->dataType = $typename;
        }
        return $this;
    }

    /**
     * @return AttributeDataType
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    public function setSchemaName($schema)
    {
        $this->schemaName = $schema;
        return $this;
    }

    public function getSchemaName()
    {
        return $this->schemaName;
    }
}
