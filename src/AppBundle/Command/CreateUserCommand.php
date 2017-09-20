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
            ->setName('omgdb:user:create');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();
        $questionHelper = new QuestionHelper();

        $user->setUsername(
            $questionHelper->ask($input, $output, new Question('Username?'))
        );
        $user->setEmail(
            $questionHelper->ask($input, $output, new Question('Email?'))
        );

        $question = new Question('Password?');
        $question
            ->setHidden(true)
            ->setHiddenFallback(false);
        $pw = $questionHelper->ask($input, $output, $question);

        $user
            ->setPassword(
                $this->getContainer()->get('security.password_encoder')
                    ->encodePassword(new User(), $pw)
            );

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
    }

}
