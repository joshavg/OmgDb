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

    public function __construct($mode = Form::MODE_NEW)
    {
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraints = [];
        if ($this->mode == Form::MODE_NEW) {
            $constraints[] = new Neo4jUniqueNameConstraint('schema');
        }
        $builder->add('name', 'text', [
            'label' => 'label.schema.name',
            'constraints' => $constraints,
            'read_only' => $this->mode == Form::MODE_EDIT
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