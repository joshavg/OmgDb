<?php
namespace AppBundle\Architecture;

use AppBundle\Entity\AttributeRepository;
use AppBundle\Entity\Instance;
use AppBundle\Entity\Property;
use AppBundle\Entity\SchemaRepository;

class InstanceFactory
{

    /**
     * @var SchemaRepository
     */
    private $schemaRepo;

    /**
     * @var AttributeRepository
     */
    private $attrRepo;

    public function __construct(SchemaRepository $schemaRepo,
                                AttributeRepository $attrRepo)
    {
        $this->schemaRepo = $schemaRepo;
        $this->attrRepo = $attrRepo;
    }

    /**
     * @param $schemaUid
     * @return Instance
     */
    public function createEmptyInstance($schemaUid)
    {
        $schema = $this->schemaRepo->fetchByUid($schemaUid);
        $attrs = $this->attrRepo->getForSchema($schema);

        $properties = [];
        foreach($attrs as $attr) {
            $prop = new Property();
            $prop->setAttribute($attr);
            $properties[] = $prop;
        }

        $instance = new Instance();
        return $instance->setSchema($schema)->setProperties($properties);
    }

}
