<?php

namespace AppBundle\Command;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUserCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('omgdb:user:create')
            ->addOption('name', null, InputOption::VALUE_REQUIRED)
            ->addOption('email', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption('name');
        $email = $input->getOption('email') ?: 'admin@admin.org';
        $pw = 'admin';

        $user = new User();

        if ($name) {
            $user->setUsername($name);

            $question = new Question('Password?');
            $question->setHidden(true)
                ->setHiddenFallback(false);
            $pw = (new QuestionHelper())->ask($input, $output, $question);
        } else {
            $user->setUsername('admin');
        }

        $user
            ->setPassword(
                $this->getContainer()->get('security.password_encoder')
                    ->encodePassword(new User(), $pw)
            )
            ->setEmail($email);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
    }

}
