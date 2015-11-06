<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jUniqueNameConstraint;
use AppBundle\Form\ServiceForm;
use AppBundle\Form\FormDefinition;

class SchemaType extends AbstractType
{
    use ServiceForm;

    public function __construct($mode = FormDefinition::MODE_NEW)
    {
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraints = [];
        if ($this->mode == FormDefinition::MODE_NEW) {
            $constraints[] = new Neo4jUniqueNameConstraint('schema');
        }
        $builder->add('name', TextType::class, [
            'label' => 'label.schema.name',
            'constraints' => $constraints,
            'attr' => [
                'readonly' => $this->mode == FormDefinition::MODE_EDIT
            ]
        ]);
        $builder->add('save', SubmitType::class, [
            'label' => 'label.save'
        ]);
    }

    public function getName()
    {
        return 'Schema';
    }
}
