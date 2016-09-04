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
        foreach ($attrs as $attr) {
            $prop = new Property();
            $prop->setAttributeUid($attr->getUid());
            $properties[] = $prop;
        }

        $instance = new Instance();
        $instance->setSchemaUid($schemaUid)->setProperties($properties);

        return $instance;
    }

    public function createDataArray(Instance $instance)
    {
        $dat = [
            'name' => $instance->getName(),
            'schemauid' => $instance->getSchemaUid()
        ];

        foreach ($instance->getProperties() as $prop) {
            $dat[$prop->getFormFieldName()] = $prop->getValue();
        }

        return $dat;
    }

    public function createFromDataArray(array $data)
    {
        $instance = $this->createEmptyInstance($data['schemauid']);
        $instance->setName($data['name']);

        foreach ($instance->getProperties() as $prop) {
            $prop->setValue($data[$prop->getFormFieldName()]);
        }

        return $instance;
    }

}
