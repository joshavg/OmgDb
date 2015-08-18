<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\SchemaType;
use AppBundle\Architecture\RepositoryServices;
use AppBundle\Form\FormDefinition;
use AppBundle\Entity\Schema;

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
     * @Template("AppBundle:Schema:new.html.twig")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function insertSchemaAction(Request $request)
    {
        $form = $this->createForm(new SchemaType(), new Schema());
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
        $form = $this->createForm(new SchemaType(), new Schema(), [
            'action' => $this->generateUrl('schema_insert')
        ]);
        return [
            'schemas' => $dat,
            'newform' => $form->createView()
        ];
    }

    /**
     * @Route("/{name}/edit", name="schema_edit")
     * @Template()
     */
    public function editAction($name)
    {
        $schema = $this->getSchemaRepository()->fetch($name);
        $form = $this->createForm(new SchemaType(FormDefinition::MODE_EDIT), $schema, [
            'action' => $this->generateUrl('schema_update')
        ]);

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/update", methods={"POST", "PUT"}, name="schema_update")
     * @Template("AppBundle:Schema:edit.html.twig")
     */
    public function updateAction(Request $req)
    {
        $form = $this->createForm(new SchemaType(FormDefinition::MODE_EDIT), new Schema());

        $form->handleRequest($req);
        if ($form->isValid()) {
            return $this->redirect($this->generateUrl('schema_index'));
        }

        return [
            'form' => $form->createView()
        ];
    }
}
