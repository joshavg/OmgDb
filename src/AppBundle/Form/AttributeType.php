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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Entity\Attribute;
use AppBundle\Entity\AttributeDataType;
use AppBundle\Entity\Schema;

class AttributeType extends AbstractType
{
    use ServiceForm;

    public function __construct($mode = FormDefinition::MODE_NEW)
    {
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('schema', 'text', [
            'label' => 'label.attribute.schema',
            'read_only' => true,
            'data_class' => Schema::class
        ]);
        
        $builder->add('name', 'text', [
            'label' => 'label.attribute.name'
        ]);
        
        $choices = [];
        foreach(AttributeDataType::getTypes() as $type)
        {
            $choices[$type->getName()] = $type->getName();
        }
        $builder->add('dataType', 'choice', [
            'choices' => $choices
    	]);
    }

    public function getName()
    {
        return 'Attribute';
    }

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Attribute::class
        ]);
    }
}