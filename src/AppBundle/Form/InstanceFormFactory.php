<?php

namespace AppBundle\Form;

use AppBundle\Entity\AttributeDataType;
use AppBundle\Entity\Instance;
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
     * @param Instance $instance
     * @param string $action
     * @return Form
     */
    public function createForm(Instance $instance, $action)
    {
        $builder = $this->ff->createBuilder();

        $builder->add('schemaName', TextType::class, [
            'label' => 'label.schema.name',
            'attr' => [
                'readonly' => true
            ]
        ]);

        $builder->add('name', TextType::class, [
            'label' => 'label.instance.name',
            'required' => true
        ]);

        foreach ($instance->getAttributes() as $attr) {
            $type = $attr->getDataType();
            $builder->add($attr->getFormFieldName(), static::getFieldType($type), [
                'label' => $attr->getName(),
                'required' => false
            ]);
        }

        $builder->setAction($action);
        $builder->setData($instance->toArray());

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

}
