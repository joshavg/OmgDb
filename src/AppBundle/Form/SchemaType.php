<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jUniqueLabelConstraint;
use Symfony\Component\Validator\Constraints\Regex;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jLabelConstraint;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jCallbackConstraint;
use laniger\Neo4jBundle\Architecture\Neo4jClientWrapper;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jUniqueNameConstraint;

class SchemaType extends AbstractType
{
    use ServiceForm;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', [
            'label' => 'label.schema.name',
            'constraints' => [
                new Neo4jUniqueNameConstraint('schema')
            ]
        ]);
        $builder->add('save', 'submit', [
            'label' => 'label.save'
        ]);
    }

    public function getName()
    {
        return 'Schema';
    }
}