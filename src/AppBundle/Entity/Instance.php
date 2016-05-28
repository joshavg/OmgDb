<?php

namespace AppBundle\Entity;

class Instance
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var Attribute[]
     */
    private $attributes;

    /**
     * @var mixed[]
     */
    private $attributeData;

    /**
     * @var string
     */
    private $uid;

    public function __construct()
    {
        $this->uid = uniqid('instance');
    }

    /**
     * @return \mixed[]
     */
    public function getAttributeData()
    {
        return $this->attributeData;
    }

    /**
     * @param \mixed[] $attributeData
     * @return Instance
     */
    public function setAttributeData($attributeData)
    {
        $this->attributeData = $attributeData;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Instance
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param Schema $schema
     * @return Instance
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
        return $this;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param Attribute[] $attributes
     * @return Instance
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return string
     */
    public function getSchemaName()
    {
        return $this->schema->getName();
    }

}
