<?php

namespace AppBundle\Service;


use AppBundle\Entity\Attribute;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;

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
     * @param Attribute[] $attributes
     * @return \Symfony\Component\Form\FormInterface
     */
    public function build(array $attributes)
    {
        $form = $this->factory->create()
            ->add('name');

        foreach ($attributes as $attr) {
            $form->add($attr->getName(), static::getType($attr));
        }

        return $form;
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
}
