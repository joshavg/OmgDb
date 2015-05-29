<?php
namespace AppBundle\Form;

trait ServiceForm
{

    public static function serviceName()
    {
        $parts = explode('\\', __CLASS__);
        $class = array_pop($parts);
        return str_replace('Type', '', $class);
    }
}