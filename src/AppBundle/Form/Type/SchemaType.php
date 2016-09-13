<?php
namespace AppBundle\Form\Type;

use AppBundle\Validator\Constraints\SchemaNameConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\ServiceForm;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchemaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => 'label.schema.name'
        ]);

        $builder->add('save', SubmitType::class, [
            'label' => 'label.save'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('validation_groups', [
            'insert'
        ]);
    }

    public function getBlockPrefix()
    {
        return $this->getName();
    }

    public function getName()
    {
        return 'Schema';
    }
}
