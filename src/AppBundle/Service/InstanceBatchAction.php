<?php

namespace AppBundle\Service;


use AppBundle\DomainCommand\AddTagToInstance;
use AppBundle\DomainCommand\DeleteInstance;
use AppBundle\DomainCommand\RemoveTagFromInstance;
use AppBundle\Entity\Instance;
use AppBundle\Entity\Tag;

class InstanceBatchAction
{

    /**
     * @var AddTagToInstance
     */
    private $atti;
    /**
     * @var RemoveTagFromInstance
     */
    private $rtfi;
    /**
     * @var DeleteInstance
     */
    private $di;

    public function __construct(AddTagToInstance $atti, RemoveTagFromInstance $rtfi, DeleteInstance $di)
    {
        $this->atti = $atti;
        $this->rtfi = $rtfi;
        $this->di = $di;
    }

    /**
     * @param Instance[] $instances
     * @param Tag $tag
     */
    public function assignTag(array $instances, Tag $tag)
    {
        foreach ($instances as $instance) {
            $this->atti->execute($instance, $tag);
        }
    }

    /**
     * @param Instance[] $instances
     * @param Tag $tag
     */
    public function removeTag(array $instances, Tag $tag)
    {
        foreach ($instances as $instance) {
            $this->rtfi->execute($instance, $tag);
        }
    }

    /**
     * @param Instance[] $instances
     */
    public function delete(array $instances)
    {
        foreach ($instances as $instance) {
            $this->di->execute($instance);
        }
    }
}
