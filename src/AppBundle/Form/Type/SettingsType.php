<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class SettingsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => 'label.user.name',
            'attr' => [
                'readonly' => true
            ]
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'label.email'
        ]);
        $builder->add('password', PasswordType::class, [
            'constraints' => [
                new UserPassword()
            ],
            'label' => 'label.password',
            'mapped' => false
        ]);
        $builder->add('newpassword', RepeatedType::class, [
            'mapped' => false,
            'required' => false,
            'type' => PasswordType::class,
            'first_options' => [
                'label' => 'label.new.password'
            ],
            'second_options' => [
                'label' => 'label.new.password.repeat'
            ]
        ]);
        $builder->add('save', SubmitType::class, [
            'label' => 'label.save'
        ]);
    }

    public function getBlockPrefix()
    {
        return $this->getName();
    }

    public function getName()
    {
        return 'SettingsType';
    }
}
