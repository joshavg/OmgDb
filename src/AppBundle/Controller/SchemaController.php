<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Schema;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Schema controller.
 *
 * @Route("schema")
 */
class SchemaController extends Controller
{
    /**
     * Lists all Schema entities.
     *
     * @Route("/", name="schema_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $schemas = $em->getRepository('AppBundle:Schema')->findAll();

        return [
            'schemas' => $schemas
        ];
    }

    /**
     * Creates a new Schema entity.
     *
     * @Route("/new", name="schema_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $schema = new Schema();
        $schema->setCreatedBy($this->getUser());
        $form = $this->createForm('AppBundle\Form\SchemaType', $schema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($schema);
            $em->flush();

            return $this->redirectToRoute('schema_show',
                ['id' => $schema->getId()]);
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * Finds and displays a Schema entity.
     *
     * @Route("/{id}", name="schema_show")
     * @Method("GET")
     * @Template
     */
    public function showAction(Schema $schema)
    {
        $deleteForm = $this->createDeleteForm($schema);

        return [
            'schema' => $schema,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing Schema entity.
     *
     * @Route("/{id}/edit", name="schema_edit")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function editAction(Request $request, Schema $schema)
    {
        $deleteForm = $this->createDeleteForm($schema);
        $editForm = $this->createForm('AppBundle\Form\SchemaType', $schema);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('schema_edit',
                ['id' => $schema->getId()]);
        }

        return [
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a Schema entity.
     *
     * @Route("/{id}", name="schema_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Schema $schema)
    {
        $form = $this->createDeleteForm($schema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($schema);
            $em->flush();
        }

        return $this->redirectToRoute('schema_index');
    }

    /**
     * Creates a form to delete a Schema entity.
     *
     * @param Schema $schema The Schema entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Schema $schema)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('schema_delete', array('id' => $schema->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
