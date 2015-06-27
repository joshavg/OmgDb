<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\NewSchemaType;
use AppBundle\Architecture\RepositoryServices;

/**
 * @Route("/schema")
 *
 * @author laniger
 */
class SchemaController extends Controller
{
    use RepositoryServices;

    /**
     * @Route("/new", methods={"POST"}, name="schema_insert")
     * @Template(template="AppBundle:Schema:new.html.twig")
     *
     * @param Request $request
     */
    public function insertSchemaAction(Request $request)
    {
        $form = $this->createForm(NewSchemaType::serviceName());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getSchemaRepository()->newSchema($form->getData());
            return $this->redirect($this->generateUrl('schema_index'));
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/index", name="schema_index")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        $dat = $this->getSchemaRepository()->fetchForOverview();
        return [
            'schemas' => $dat
        ];
    }
}