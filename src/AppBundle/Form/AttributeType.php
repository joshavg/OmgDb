<?php

namespace AppBundle\Form;

use AppBundle\Entity\Schema;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AttributeType extends AbstractType
{

    /**
     * @var TokenStorageInterface
     */
    private $storage;

    public function __construct(TokenStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('datatype', ChoiceType::class, [
                'choices' => [
                    'text' => 'text',
                    'number' => 'number',
                    'date' => 'date'
                ]
            ])
            ->add('schema', EntityType::class, [
                'class' => Schema::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('s')
                        ->where('s.createdBy = :u')
                        ->setParameters([
                            'u' => $this->storage->getToken()->getUser()
                        ])
                        ->orderBy('s.name');
                }
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Attribute'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_attribute';
    }


}
