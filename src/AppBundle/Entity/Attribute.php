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

    private $schemaUid;
    
    private $uid;
    
    private $schemaName;

    public function getSchemaName()
    {
        return $this->schemaName;
    }

    public function setSchemaName($schemaName)
    {
        $this->schemaName = $schemaName;
        return $this;
    }

    public function __construct()
    {
        $this->dataType = AttributeDataType::getByName(AttributeDataType::$TEXT);
        $this->uid = uniqid('attribute', true);
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function setUid($uid)
    {
        $this->uid = $uid;
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

    public function setSchemaUid($uid)
    {
        $this->schemaUid = $uid;
        return $this;
    }

    public function getSchemaUid()
    {
        return $this->schemaUid;
    }
}
