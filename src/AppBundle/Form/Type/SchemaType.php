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
        $builder->add('save', SubmitType::class, [
            'label' => 'label.save'
        ]);

        $readonly = in_array('update', $options['validation_groups']);

        $builder->add('name', TextType::class, [
            'label' => 'label.schema.name',
            'attr' => [
                'readonly' => $readonly
            ],
            'position' => 'first'
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
