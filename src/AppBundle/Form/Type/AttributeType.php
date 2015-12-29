<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jUniqueLabelConstraint;
use Symfony\Component\Validator\Constraints\Regex;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jLabelConstraint;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jCallbackConstraint;
use laniger\Neo4jBundle\Architecture\Neo4jClientWrapper;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jUniqueNameConstraint;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Entity\Attribute;
use AppBundle\Entity\AttributeDataType;
use AppBundle\Entity\Schema;
use AppBundle\Form\FormDefinition;
use AppBundle\Form\ServiceForm;

class AttributeType extends AbstractType
{
    use ServiceForm;

    public function __construct($mode = FormDefinition::MODE_NEW)
    {
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('schema', TextType::class, [
            'label' => 'label.attribute.schema',
            'attr' => [
                'readonly' => true
            ],
            'data_class' => Schema::class
        ]);

        $builder->add('name', TextType::class, [
            'label' => 'label.attribute.name'
        ]);

        $choices = [];
        foreach (AttributeDataType::getTypes() as $type) {
            $choices[$type->getName()] = $type->getName();
        }
        $builder->add('dataType', ChoiceType::class, [
            'choices' => $choices
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Attribute::class
        ]);
    }
}
