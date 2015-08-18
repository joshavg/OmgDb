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

class SchemaFilterType extends AbstractType
{
    use ServiceForm;

    private $schemas;
    
    public function __construct(array $schemas = null)
    {
        $this->schemas = $schemas;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];
        foreach($this->schemas as $schema)
        {
            $choices[$schema->getName()] = $schema->getName();
        }
        
        $builder->add('schema', 'choice', [
            'choices' => $choices,
            'empty_data' => null,
            'placeholder' => 'label.attribute.choose-schema'
    	]);
    }

    public function getName()
    {
        return 'SchemaFilter';
    }
}