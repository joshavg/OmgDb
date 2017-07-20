<?php

namespace AppBundle\Form;

use AppBundle\Entity\Schema;
use AppBundle\Validator\Constraint\CsvFile;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ImportType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    private $storage;

    public function __construct(TokenStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'constraints' => [
                    new CsvFile()
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

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'app_bundle_import_type';
    }
}
