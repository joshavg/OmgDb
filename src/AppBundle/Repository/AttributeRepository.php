<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\Schema;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * AttributeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AttributeRepository extends EntityRepository
{

    /**
     * @param Schema $schema
     * @return Attribute[]
     */
    public function findFromSchema(Schema $schema)
    {
        return $this->findBy([
            'schema' => $schema
        ], [
            'name' => 'ASC'
        ]);
    }

    /**
     * @param Attribute $attr
     * @param User $user
     * @throws AccessDeniedException
     */
    public function assertBelongsToUser(Attribute $attr, User $user)
    {
        $belongs = $attr->getSchema()->getCreatedBy()->getId() === $user->getId();
        if(!$belongs) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @param User $user
     * @return Attribute[]
     */
    public function findFromUser(User $user)
    {
        return $this->createQueryBuilder('a')
            ->join('a.schema', 's')
            ->join('s.createdBy', 'u', 'WITH', 'u = :u')
            ->orderBy('a.name')
            ->setParameters([
                'u' => $user
            ])
            ->getQuery()
            ->execute();
    }

}
