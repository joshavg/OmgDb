<?php
namespace AppBundle\Entity;

use HireVoice\Neo4j\Annotation as OGM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * OGM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 */
class User implements UserInterface, \Serializable
{

    /**
     * OGM\Auto
     *
     * @var int
     */
    private $id;

    /**
     * OGM\Property
     * OGM\Index
     *
     * @var string
     */
    private $name;

    /**
     * OGM\Property
     *
     * @var string
     */
    private $email;

    /**
     * OGM\Property
     *
     * @var string
     */
    private $password;

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

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserInterface::getRoles()
     */
    public function getRoles()
    {
        return [
            'ROLE_USER'
        ];
    }

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserInterface::getPassword()
     */
    public function getPassword()
    {
        return $this->password;
    }

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserInterface::getSalt()
     */
    public function getSalt()
    {
        return null;
    }

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserInterface::getUsername()
     */
    public function getUsername()
    {
        return $this->name;
    }

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserInterface::eraseCredentials()
     */
    public function eraseCredentials()
    {}

    /**
     *
     * @param
     *            $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function serialize()
    {
        return serialize([
            $this->name,
            $this->email,
            $this->password
        ]);
    }

    public function unserialize($serialized)
    {
        list ($this->name, $this->email, $this->password) = unserialize($serialized);
    }
}
