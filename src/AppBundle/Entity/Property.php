<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Property
 *
 * @ORM\Table(name="property")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PropertyRepository")
 */
class Property
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * This value is always serialized - getter and setter will serialize/unserialize
     *
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;

    /**
     * @var Instance
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Instance")
     */
    private $instance;

    /**
     * @var Attribute
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Attribute")
     */
    private $attribute;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Property
     */
    public function setValue($value)
    {
        $this->value = serialize($value);

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return unserialize($this->value);
    }

    /**
     * @return Instance
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param Instance $instance
     * @return Property
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
        return $this;
    }

    /**
     * @return Attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param Attribute $attribute
     * @return Property
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

}
