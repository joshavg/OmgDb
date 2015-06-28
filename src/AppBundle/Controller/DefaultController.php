<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Architecture\ContainerServices;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\SchemaType;

class DefaultController extends Controller
{
    use ContainerServices;

    /**
     * @Route("/", name="index")
     * @Template
     */
    public function indexAction(Request $req)
    {
        $form = $this->createForm(SchemaType::serviceName());

        $form->handleRequest($req);
        $form->isValid();

        return [
            'newSchemaForm' => $form->createView()
        ];
    }
}
