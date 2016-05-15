<?php
namespace AppBundle\Entity;

/**
 * @AppBundle\Validator\Constraints\SchemaName
 */
class Schema
{
    private $name;

    private $createdAt;

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

    public function __toString()
    {
        return $this->name;
    }
}
