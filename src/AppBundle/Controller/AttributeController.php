<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Attribute;
use AppBundle\Form\AttributeType;
use AppBundle\Form\SchemaSelectType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * AppBundle.Resources.views.Attribute controller.
 *
 * @Route("attribute")
 */
class AttributeController extends Controller
{
    /**
     * Lists all attribute entities.
     *
     * @Route("/", name="attribute_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $schemaSelect = $this->createForm(SchemaSelectType::class, [], [
            'method' => 'GET',
            'csrf_protection' => false
        ]);

        $id = null;
        if($schemaSelect->handleRequest($request)->isSubmitted()) {
            $id = $schemaSelect->getData()['schema']->getId();
        }

        if ($id !== null) {
            $schema = $em
                ->getRepository('AppBundle:Schema')
                ->find($id);
            $attributes = $em
                ->getRepository('AppBundle:Attribute')
                ->findFromSchema($schema);
        } else {
            $attributes = $em
                ->getRepository('AppBundle:Attribute')
                ->findFromUser($this->getUser());
        }


        return [
            'schemaSelect' => $schemaSelect->createView(),
            'attributes' => $attributes,
        ];
    }

    /**
     * Creates a new attribute entity.
     *
     * @Route("/new", name="attribute_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $attribute = new Attribute();
        $form = $this->createForm(AttributeType::class, $attribute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($attribute);
            $em->flush();

            return $this->redirectToRoute('attribute_show',
                ['id' => $attribute->getId()]);
        }

        return [
            'attribute' => $attribute,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a attribute entity.
     *
     * @Route("/{id}", name="attribute_show")
     * @Method("GET")
     * @Template
     */
    public function showAction(Attribute $attribute)
    {
        $this->getDoctrine()
            ->getRepository('AppBundle:Attribute')
            ->assertBelongsToUser($attribute, $this->getUser());

        $deleteForm = $this->createDeleteForm($attribute);

        return [
            'attribute' => $attribute,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing attribute entity.
     *
     * @Route("/{id}/edit", name="attribute_edit")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function editAction(Request $request, Attribute $attribute)
    {
        $this->getDoctrine()
            ->getRepository('AppBundle:Attribute')
            ->assertBelongsToUser($attribute, $this->getUser());

        $deleteForm = $this->createDeleteForm($attribute);
        $editForm = $this->createForm(AttributeType::class, $attribute);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('attribute_index');
        }

        return [
            'attribute' => $attribute,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a attribute entity.
     *
     * @Route("/{id}", name="attribute_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Attribute $attribute)
    {
        $this->getDoctrine()
            ->getRepository('AppBundle:Attribute')
            ->assertBelongsToUser($attribute, $this->getUser());

        $form = $this->createDeleteForm($attribute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($attribute);
            $em->flush();
        }

        return $this->redirectToRoute('attribute_index');
    }

    /**
     * Creates a form to delete a attribute entity.
     *
     * @param Attribute $attribute The attribute entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Attribute $attribute)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('attribute_delete', ['id' => $attribute->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
