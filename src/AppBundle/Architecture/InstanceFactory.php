<?php
namespace AppBundle\Architecture;

use AppBundle\Entity\AttributeRepository;
use AppBundle\Entity\Instance;
use AppBundle\Entity\Property;
use AppBundle\Entity\SchemaRepository;
use Symfony\Component\Validator\Constraints\DateTime;

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
        $instance->setSchemaUid($schemaUid)
                 ->setProperties($properties)
                 ->setCreatedAt(new \DateTime());

        return $instance;
    }

    public function createDataArray(Instance $instance)
    {
        $dat = [
            'name' => $instance->getName(),
            'schemauid' => $instance->getSchemaUid(),
            'created_at' => $instance->getCreatedAt()->format(\DateTime::ISO8601),
            'instanceuid' => $instance->getUid()
        ];

        foreach ($instance->getProperties() as $prop) {
            $dat[$prop->getFormFieldName()] = $prop->getValue();
            $dat[$prop->getFormFieldName() . '-uid'] = $prop->getUid();
        }

        return $dat;
    }

    public function createFromDataArray(array $data)
    {
        $instance = $this->createEmptyInstance($data['schemauid']);
        $instance->setUid($data['instanceuid']);
        $instance->setName($data['name']);
        $instance->setCreatedAt(\DateTime::createFromFormat(\DateTime::ISO8601,
                                                            $data['created_at']));

        foreach ($instance->getProperties() as $prop) {
            $prop->setValue($data[$prop->getFormFieldName()]);
            $prop->setUid($data[$prop->getFormFieldName() . '-uid']);
        }

        return $instance;
    }

    public function createDuplicate(Instance $instance)
    {
        $copy = clone $instance;
        foreach ($instance->getProperties() as $prop) {
            $copy->addProperty(clone $prop);
        }
        return $copy;
    }

}
