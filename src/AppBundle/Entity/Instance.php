<?php

namespace AppBundle\Entity;

class Instance
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $schemauid;

    /**
     * @var string
     */
    private $uid;

    /**
     * @var \DateTime
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
     * @return string
     */
    public function getSchemaUid()
    {
        return $this->schemauid;
    }

    /**
     * @param string $uid
     * @return Instance
     */
    public function setSchemaUid($uid)
    {
        $this->schemauid = $uid;
        return $this;
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
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
