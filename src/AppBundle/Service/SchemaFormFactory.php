<?php

namespace AppBundle\Service;


use AppBundle\Entity\Attribute;
use AppBundle\Entity\Instance;
use AppBundle\Entity\Property;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class SchemaFormFactory
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param Attribute $attr
     * @param Property[] $properties
     * @return null
     */
    private static function fetchPropertyValue(Attribute $attr, $properties)
    {
        if ($properties === null) {
            return null;
        }

        foreach ($properties as $prop) {
            if ($prop->getAttribute()->getId() == $attr->getId()) {
                return unserialize($prop->getValue());
            }
        }

        return null;
    }

    /**
     * @param Attribute[] $attributes
     * @param $action
     * @param Instance|null $instance
     * @param Property[]|null $properties
     * @return FormInterface
     */
    public function form(array $attributes, $action, Instance $instance = null, array $properties = null)
    {
        $form = $this->factory->createBuilder()
            ->setAction($action)
            ->add('_name', TextType::class, [
                'data' => $instance === null ? '' : $instance->getName()
            ]);

        foreach ($attributes as $attr) {
            $form->add($attr->getId(), static::getType($attr), [
                'label' => $attr->getName(),
                'data' => static::fetchPropertyValue($attr, $properties)
            ]);
        }

        return $form->getForm();
    }

    private static function getType(Attribute $attr)
    {
        switch ($attr->getDatatype()) {
            case 'number':
                return NumberType::class;
            case 'date':
                return DateType::class;
            default:
                return TextType::class;
        }
    }

    /**
     * @param FormInterface $form
     * @param Instance|null $instance
     * @return Instance
     */
    public function instance(FormInterface $form, Instance $instance = null)
    {
        $instance = $instance ?: new Instance();
        return $instance->setName($form->getData()['_name']);
    }

    /**
     * @param FormInterface $form
     * @param Instance $instance
     * @param Attribute[] $attributes
     * @param Property[] $properties
     * @return Property[]
     */
    public function properties(FormInterface $form, Instance $instance, array $attributes,
                               array $properties = null)
    {
        $props = [];
        $data = $form->getData();

        foreach ($attributes as $attr) {
            $prop = null;
            if ($properties) {
                $prop = array_filter($properties, function (Property $p) use ($attr) {
                    return $p->getAttribute()->getId() == $attr->getId();
                })[0];
            } else {
                $prop = new Property();
            }

            $dat = $data[$attr->getId()];
            $prop
                ->setAttribute($attr)
                ->setInstance($instance)
                ->setValue(serialize($dat));
            $props[] = $prop;
        }

        return $props;
    }
}
