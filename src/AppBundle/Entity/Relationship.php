<?php

namespace AppBundle\Entity;


class Relationship
{

    /**
     * @var Instance
     */
    private $from;

    /**
     * @var Instance
     */
    private $to;

    /**
     * @var string
     */
    private $label;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @return Instance
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param Instance $from
     * @return Relationship
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return Instance
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param Instance $to
     * @return Relationship
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return Relationship
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return Relationship
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

}
