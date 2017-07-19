<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * Tag
 *
 * @ORM\Table(name="Tag",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uq_name_user", columns={"name", "created_by_id"})
 *     })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagRepository")
 */
class Tag
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    private $createdBy;

    /**
     * @var Instance[]|PersistentCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Instance", mappedBy="tags")
     */
    private $instances;


    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Tag
     */
    public function setName($name): Tag
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return User
     */
    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     * @return Tag
     */
    public function setCreatedBy(User $createdBy)
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return Instance[]|PersistentCollection
     */
    public function getInstances(): PersistentCollection
    {
        return $this->instances;
    }

    /**
     * @param Instance[] $instances
     * @return Tag
     */
    public function setInstances($instances): Tag
    {
        $this->instances = $instances;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

}

