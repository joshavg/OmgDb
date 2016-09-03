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
     * @var string
     */
    private $uid;

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @var Property[]
     */
    private $properties;

    public function __construct()
    {
        $this->uid = uniqid('instance');
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
     * @return string
     */
    public function getSchemaName()
    {
        return $this->schema->getName();
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     * @return Instance
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return Instance
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Property[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param Property[] $properties
     * @return Instance
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * @param Property $p
     * @return Instance
     */
    public function addProperty(Property $p)
    {
        $this->properties[] = $p;
        return $this;
    }

}
