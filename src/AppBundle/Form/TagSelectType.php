<?php

namespace AppBundle\Form;


use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TagSelectType extends AbstractType
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
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('t')
                        ->where('t.createdBy = :u')
                        ->setParameters([
                            'u' => $this->storage->getToken()->getUser()
                        ])
                        ->orderBy('t.name');
                }
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_schemaselect';
    }

}
