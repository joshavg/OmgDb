<?php

namespace AppBundle\Form;

use AppBundle\Architecture\InstanceFactory;
use AppBundle\Entity\AttributeDataType;
use AppBundle\Entity\AttributeRepository;
use AppBundle\Entity\Instance;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;

class InstanceFormFactory
{
    /**
     * @var FormFactory
     */
    private $ff;

    /**
     * @var InstanceFactory
     */
    private $if;

    /**
     * @var AttributeRepository
     */
    private $attrrepo;

    public function __construct(FormFactory $ff, InstanceFactory $if, AttributeRepository $attrrepo)
    {
        $this->ff = $ff;
        $this->if = $if;
        $this->attrrepo = $attrrepo;
    }

    /**
     * @param Instance $instance
     * @param string $action
     * @return FormInterface
     */
    public function createForm(Instance $instance, $action)
    {
        $builder = $this->ff->createBuilder();

        $builder->add('schemauid', HiddenType::class);
        $builder->add('created_at', HiddenType::class);
        $builder->add('instanceuid', HiddenType::class);

        $builder->add('name', TextType::class, [
            'label' => 'label.instance.name',
            'required' => true
        ]);

        foreach ($instance->getProperties() as $prop) {
            $attr = $this->attrrepo->fetchByUid($prop->getAttributeUid());
            $type = $attr->getDataType();

            $builder->add($prop->getFormFieldName() . '-uid', HiddenType::class);
            $builder->add($prop->getFormFieldName(), static::getFieldType($type), [
                'label' => $attr->getName(),
                'required' => false,
                'attr' => static::getExtraAttributes($type)
            ]);
        }

        $builder->setAction($action);
        $builder->setData($this->if->createDataArray($instance));

        return $builder->getForm();
    }

    private static function getExtraAttributes(AttributeDataType $type)
    {
        if ($type->getName() === AttributeDataType::MARKDOWN) {
            return [
                'rows' => 10
            ];
        }

        return [];
    }

    private static function getFieldType(AttributeDataType $type)
    {
        switch ($type->getName()) {
            case AttributeDataType::BOOLEAN:
                return CheckboxType::class;
            case AttributeDataType::NUMBER:
                return NumberType::class;
            case AttributeDataType::TEXT:
            case AttributeDataType::MARKDOWN:
                return TextareaType::class;
            default:
                return TextType::class;
        }
    }

}
