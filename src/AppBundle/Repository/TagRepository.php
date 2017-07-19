<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends EntityRepository
{

    /**
     * @param User $user
     * @return Tag[]
     */
    public function findFromUser(User $user)
    {
        return $this->findBy([
            'createdBy' => $user
        ], [
            'name' => 'ASC'
        ]);
    }

    /**
     * @param Tag $tag
     * @param User $user
     * @throws AccessDeniedException
     */
    public function assertBelongsToUser(Tag $tag, User $user)
    {
        if($tag->getCreatedBy()->getId() !== $user->getId()) {
            throw new AccessDeniedException();
        }
    }

}
