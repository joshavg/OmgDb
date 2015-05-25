<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Architecture\ContainerServices;
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
        $form = $this->createForm('NewSchema');

        $form->handleRequest($req);
        $form->isValid();

        return [
            'newSchemaForm' => $form->createView()
        ];
    }

    private function newSchemaForm()
    {
        return $this->createForm('NewSchema', [], []);
    }
}
