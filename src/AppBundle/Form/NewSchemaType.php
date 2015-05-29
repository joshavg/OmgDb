<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jUniqueLabelConstraint;
use Symfony\Component\Validator\Constraints\Regex;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jLabelConstraint;
use AppBundle\Architecture\RoutedForm;

class NewSchemaType extends RoutedForm
{
    use ServiceForm;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('uniqueName', 'text', [
            'label' => 'label.unique.name',
            'constraints' => [
                new Neo4jLabelConstraint(),
                new Neo4jUniqueLabelConstraint()
            ]
        ]);
        $builder->add('save', 'submit', [
            'label' => 'label.save'
        ]);
    }

    public function getName()
    {
        return 'NewSchema';
    }
}