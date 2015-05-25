<?php
namespace AppBundle\Architecture;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormBuilderInterface;

trait RoutedForm
{

    private $url;

    public function __construct(Router $router, $route)
    {
        $this->url = $router->generate($route);
    }

    public function setRoute(FormBuilderInterface $builder)
    {
        $builder->setAction($this->url);
    }
}