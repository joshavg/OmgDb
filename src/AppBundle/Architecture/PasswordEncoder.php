<?php
namespace AppBundle\Architecture;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class PasswordEncoder implements PasswordEncoderInterface
{

    public function encodePassword($raw, $salt)
    {
        return password_hash($raw, PASSWORD_DEFAULT);
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        return password_verify($raw, $encoded);
    }
}