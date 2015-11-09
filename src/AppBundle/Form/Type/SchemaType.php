<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jUniqueNameConstraint;
use AppBundle\Form\ServiceForm;
use AppBundle\Form\FormDefinition;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SchemaType extends AbstractType
{
    use ServiceForm;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('save', SubmitType::class, [
            'label' => 'label.save'
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $constraints = [];
            $readonly = false;
            if ($event->getData() && $event->getData()->getCreatedAt()) {
                $readonly = true;
            } else {
                $constraints[] = new Neo4jUniqueNameConstraint('schema');
            }

            $event->getForm()->add('name', TextType::class, [
                'label' => 'label.schema.name',
                'constraints' => $constraints,
                'attr' => [
                    'readonly' => $readonly
                ],
                'position' => 'first'
            ]);
        });
    }

    public function getName()
    {
        return 'Schema';
    }
}
