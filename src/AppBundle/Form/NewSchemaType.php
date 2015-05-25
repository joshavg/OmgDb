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

class NewSchemaType extends AbstractType
{
    use RoutedForm;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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

        $this->setRoute($builder);
    }

    public function getName()
    {
        return 'NewSchema';
    }
}