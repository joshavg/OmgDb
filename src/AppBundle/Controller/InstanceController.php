<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Instance;
use AppBundle\Entity\Schema;
use AppBundle\Service\SchemaFormFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/instance")
 */
class InstanceController extends Controller
{

    /**
     * @Route("/listschema/{id}", name="instance_index")
     * @Template
     *
     * @param Schema $schema
     * @return array
     */
    public function indexAction(Schema $schema)
    {
        $instances = $this->getDoctrine()
            ->getRepository('AppBundle:Instance')
            ->findFromSchemaAndUser($schema, $this->getUser());

        return [
            'instances' => $instances,
            'schema' => $schema
        ];
    }

    /**
     * @Route("/{id}/new", name="instance_new")
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @param SchemaFormFactory $sff
     * @param Schema $schema
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request, SchemaFormFactory $sff, Schema $schema)
    {
        $attributes = $this->fetchAttributes($schema);

        $form = $sff->form(
            $attributes,
            $this->generateUrl('instance_new', [
                'id' => $schema->getId()
            ])
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $instance = $sff->instance($form);
            $instance
                ->setSchema($schema)
                ->setCreatedBy($this->getUser());
            $em->persist($instance);

            $properties = $sff->properties($form, $instance, $attributes);
            foreach ($properties as $prop) {
                $em->persist($prop);
            }

            $em->flush();

            return $this->redirectToRoute('instance_index',
                ['id' => $schema->getId()]);
        }

        return [
            'schema' => $schema,
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/{id}", name="instance_show")
     * @Method("GET")
     * @Template
     *
     * @param Instance $instance
     * @return array
     */
    public function showAction(Instance $instance)
    {
        $properties = $this->getDoctrine()
            ->getRepository('AppBundle:Property')
            ->findFromInstance($instance);

        return [
            'instance' => $instance,
            'properties' => $properties
        ];
    }

    /**
     * @Route("/{id}/edit", name="instance_edit")
     * @Template
     *
     * @param Request $request
     * @param SchemaFormFactory $sff
     * @param Instance $instance
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, SchemaFormFactory $sff, Instance $instance)
    {
        $deleteForm = $this->createDeleteForm($instance);

        $properties = $this->getDoctrine()
            ->getRepository('AppBundle:Property')
            ->findFromInstance($instance);

        $attributes = $this->fetchAttributes($instance->getSchema());
        $editForm = $sff->form(
            $attributes,
            $this->generateUrl('instance_edit', [
                'id' => $instance->getId()
            ]),
            $instance,
            $properties
        );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($sff->instance($editForm, $instance));

            foreach ($sff->properties($editForm, $instance, $attributes, $properties) as $prop) {
                $em->persist($prop);
            }

            $em->flush();

            return $this->redirectToRoute('instance_show',
                ['id' => $instance->getId()]);
        }

        return [
            'instance' => $instance,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * @Route("/{id}", name="instance_delete")
     * @Method("DELETE")
     *
     * @param Instance $instance
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Instance $instance)
    {
        $em = $this->getDoctrine()->getManager();

        $props = $this->getDoctrine()
            ->getRepository('AppBundle:Property')
            ->findFromInstance($instance);

        foreach ($props as $prop) {
            $em->remove($prop);
        }
        $em->remove($instance);
        $em->flush();

        return $this->redirectToRoute('instance_index', [
            'id' => $instance->getSchema()->getId()
        ]);
    }

    private function createDeleteForm(Instance $instance)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('instance_delete', ['id' => $instance->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Schema $schema
     * @return \AppBundle\Entity\Attribute[]|array
     */
    public function fetchAttributes(Schema $schema)
    {
        return $this->getDoctrine()
            ->getRepository('AppBundle:Attribute')
            ->findFromSchema($schema);
    }

}
