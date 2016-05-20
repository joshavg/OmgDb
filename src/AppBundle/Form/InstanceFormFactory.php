<?php

namespace AppBundle\Form;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\AttributeDataType;
use AppBundle\Entity\Schema;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;

class InstanceFormFactory
{
    /**
     * @var FormFactory
     */
    private $ff;

    public function __construct(FormFactory $ff)
    {
        $this->ff = $ff;
    }

    /**
     * @param Schema $schema
     * @param Attribute[] $attributes
     * @return Form
     */
    public function createForm(Schema $schema, array $attributes = null)
    {
        $builder = $this->ff->createBuilder();

        $builder->add('schema_name', TextType::class, [
            'label' => 'label.schema.name',
            'attr' => [
                'readonly' => true
            ]
        ]);
        $builder->add('schema_uid', TextType::class, [
            'label' => 'label.schema.uid',
            'attr' => [
                'readonly' => true
            ]
        ]);

        foreach ($attributes as $attr) {
            $type = $attr->getDataType();
            $builder->add(static::childName($attr), static::getFieldType($type), [
                'label' => $attr->getName()
            ]);
        }

        return $builder->getForm();
    }

    private static function getFieldType(AttributeDataType $type)
    {
        switch ($type->getName()) {
            case AttributeDataType::$BOOLEAN:
                return CheckboxType::class;
            case AttributeDataType::$NUMBER:
                return NumberType::class;
            case AttributeDataType::$TEXT:
            case AttributeDataType::$MARKDOWN:
                return TextareaType::class;
            default:
                return TextType::class;
        }
    }

    private static function childName(Attribute $attr)
    {
        return 'a' . $attr->getUid();
    }

}
