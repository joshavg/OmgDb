<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Upload
 *
 * @ORM\Table(name="file_import")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UploadRepository")
 */
class FileImport
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
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="uploadedAt", type="datetimetz")
     */
    private $uploadedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    private $uploadedBy;

    /**
     * @var Schema
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Schema")
     */
    private $schema;

    public function __construct()
    {
        $this->uploadedAt = new \DateTime();
    }

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
     * Set path
     *
     * @param string $path
     *
     * @return FileImport
     */
    public function setPath($path): FileImport
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set uploadedAt
     *
     * @param \DateTime $uploadedAt
     *
     * @return FileImport
     */
    public function setUploadedAt($uploadedAt): FileImport
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    /**
     * Get uploadedAt
     *
     * @return \DateTime
     */
    public function getUploadedAt(): \DateTime
    {
        return $this->uploadedAt;
    }

    /**
     * @return User
     */
    public function getUploadedBy(): User
    {
        return $this->uploadedBy;
    }

    /**
     * @param User $uploadedBy
     * @return FileImport
     */
    public function setUploadedBy(User $uploadedBy): FileImport
    {
        $this->uploadedBy = $uploadedBy;
        return $this;
    }

    /**
     * @return Schema
     */
    public function getSchema(): Schema
    {
        return $this->schema;
    }

    /**
     * @param Schema $schema
     * @return FileImport
     */
    public function setSchema(Schema $schema): FileImport
    {
        $this->schema = $schema;
        return $this;
    }

}
