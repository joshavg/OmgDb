<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class OmgdbCreateUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('omgdb:create-user')
            ->setDescription('Creates a new user for the OmgDb');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $validator = function ($value) {
            if (trim($value) == '') {
                throw new \Exception('Can not be empty');
            }

            return $value;
        };

        /** @var $helper QuestionHelper */
        $helper = $this->getHelper('question');

        $question = new Question('Username: ');
        $question->setValidator($validator);
        $username = $helper->ask($input, $output, $question);

        $question = new Question('EMail Adress: ');
        $question->setValidator($validator);
        $email = $helper->ask($input, $output, $question);

        $question = new Question('Password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $question->setValidator($validator);
        $password = $helper->ask($input, $output, $question);

        $encoder = $this->getContainer()->get('password_hash_encoder');
        $password = $encoder->encodePassword($password, null);

        $userrepo = $this->getContainer()->get('repo.user');
        $user = new User();
        $user->setEmail($email);
        $user->setName($username);
        $user->setPassword($password);

        $userrepo->createUser($user);
    }

}
