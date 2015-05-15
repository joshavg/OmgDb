<?php

namespace AppBundle\Entity;

use HireVoice\Neo4j\Annotation as OGM;

/**
 * User
 *
 * @OGM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 */
class User
{
    /**
     * @OGM\Auto
     */
    private $id;

    /**
     * @var string
     * 
     * @OGM\Property
     * @OGM\Index
     */
    private $name;

    /**
     * @var string
     *
     * @OGM\Property
     */
    private $email;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
}
