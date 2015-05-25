<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jUniqueLabelConstraint;

class NewSchemaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', [
            'label' => 'label.name',
            'constraints' => [
                new Neo4jUniqueLabelConstraint()
            ]
        ]);
        $builder->add('save', 'submit', [
            'label' => 'label.save'
        ]);
    }

    public function getName()
    {
        return 'SettingsType';
    }
}