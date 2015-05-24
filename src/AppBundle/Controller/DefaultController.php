<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Architecture\ContainerServices;
use AppBundle\Form\NewSchemaType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    use ContainerServices;

    /**
     * @Route("/", name="index")
     * @Template
     */
    public function indexAction(Request $req)
    {
        $form = $this->createForm(new NewSchemaType());

        $form->handleRequest($req);
        $form->isValid();

        return [
            'newSchemaForm' => $form->createView()
        ];
    }
}
