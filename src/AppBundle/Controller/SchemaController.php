<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\NewSchemaType;

/**
 * @Route("/schema")
 *
 * @author laniger
 */
class SchemaController extends Controller
{

    /**
     * @Route("/new", methods={"POST"})
     *
     * @param Request $request
     */
    public function newSchemaAction(Request $request)
    {
        $form = $this->createForm(NewSchemaType::serviceName());
        $form->handleRequest($request);

        if($form->isValid()) {
            return $this->renderView('bla');
        }
    }
}