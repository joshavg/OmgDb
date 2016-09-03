<?php
namespace AppBundle\Entity;

class Property
{

    /**
     * @var string
     */
    private $uid;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $attributeuid;

    /**
     * @var \DateTime
     */
    private $createdAt;

    public function __construct()
    {
        $this->uid = uniqid('property');
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
     * @return Property
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Property
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getAttributeUid()
    {
        return $this->attributeuid;
    }

    /**
     * @param string $attributeuid
     * @return Property
     */
    public function setAttributeUid($attributeuid)
    {
        $this->attributeuid = $attributeuid;
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
     * @return Property
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormFieldName()
    {
        return $this->attributeuid;
    }
}
