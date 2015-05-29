<?php
namespace AppBundle\Architecture;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

abstract class RoutedForm extends AbstractType
{

    private $url;

    public function __construct(Router $router, $route)
    {
        $this->url = $router->generate($route);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAction($this->url);
    }
}