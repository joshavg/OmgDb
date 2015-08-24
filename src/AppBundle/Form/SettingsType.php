<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class SettingsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', [
            'label' => 'label.email'
        ]);
        $builder->add('password', 'password', [
            'constraints' => [
                new UserPassword()
            ],
            'label' => 'label.password',
            'mapped' => false
        ]);
        $builder->add('newpassword', 'repeated', [
            'mapped' => false,
            'required' => false,
            'type' => 'password',
            'first_options' => [
                'label' => 'label.new.password'
            ],
            'second_options' => [
                'label' => 'label.new.password.repeat'
            ]
        ]);
        $builder->add('save', 'submit', [
            'label' => 'label.save'
        ]);
    }

    public function getName()
    {
        return 'SettingsType';
    }
}