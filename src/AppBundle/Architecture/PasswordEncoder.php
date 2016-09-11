<?php
namespace AppBundle\Architecture;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class PasswordEncoder implements PasswordEncoderInterface
{

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function encodePassword($raw, $salt)
    {
        return password_hash($raw, PASSWORD_DEFAULT);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        return password_verify($raw, $encoded);
    }
}
