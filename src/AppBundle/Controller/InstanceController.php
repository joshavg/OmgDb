<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Instance;
use AppBundle\Entity\Schema;
use AppBundle\Entity\Tag;
use AppBundle\Form\TagSelectType;
use AppBundle\Form\TagType;
use AppBundle\Service\SchemaFormFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
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
        $tagForm = $this->createForm(TagSelectType::class);

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

        if ($editForm->handleRequest($request)->isSubmitted() && $editForm->isValid()) {
            $instance = $this->saveEditedInstance($sff, $instance, $editForm, $attributes, $properties);

            return $this->redirectToRoute('instance_show',
                ['id' => $instance->getId()]);
        } elseif ($tagForm->handleRequest($request)->isSubmitted() && $tagForm->isValid()) {
            $this->addTagToInstance($tagForm, $instance);

            return $this->redirectToRoute('instance_edit', [
                'id' => $instance->getId()
            ]);
        }

        return [
            'instance' => $instance,
            'edit_form' => $editForm->createView(),
            'tag_form' => $tagForm->createView()
        ];
    }

    /**
     * @Route("/{id}/removetag/{tag_id}", name="instance_remove_tag")
     * @ParamConverter("tag", class="AppBundle:Tag", options={"id"="tag_id"})
     *
     * @param Instance $instance
     * @param Tag $tag
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeTag(Instance $instance, Tag $tag)
    {
        $instance->removeTag($tag);

        $em = $this->getDoctrine()->getManager();
        $em->persist($instance);
        $em->flush();

        return $this->redirectToRoute('instance_edit', [
            'id' => $instance->getId()
        ]);
    }

    /**
     * @param Form $tagForm
     * @param Instance $instance
     */
    private function addTagToInstance(Form $tagForm, Instance $instance)
    {
        /** @var Tag $tag */
        $tag = $tagForm->getData()['tag'];
        $instance
            ->addTag($tag)
            ->setUpdatedAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($instance);
        $em->flush();
    }

    /**
     * @Route("/taggedwith/{id}", name="instance_tagged_with")
     * @Template
     */
    public function taggedWithAction(Tag $tag)
    {
        $instances = $this->getDoctrine()
            ->getRepository('AppBundle:Instance')
            ->findFromTag($tag);

        return [
            'tag' => $tag,
            'instances' => $instances
        ];
    }

    /**
     * @param SchemaFormFactory $sff
     * @param Instance $instance
     * @param $editForm
     * @param $attributes
     * @param $properties
     * @return Instance
     */
    public function saveEditedInstance(SchemaFormFactory $sff, Instance $instance, $editForm,
                                       $attributes, $properties): Instance
    {
        $em = $this->getDoctrine()->getManager();

        $instance = $sff->instance($editForm, $instance);
        $instance->setUpdatedAt(new \DateTime());
        $em->persist($instance);

        foreach ($sff->properties($editForm, $instance, $attributes, $properties) as $prop) {
            $em->persist($prop);
        }

        $em->flush();
        return $instance;
    }

    /**
     * @Route("/{id}/delete", name="instance_delete")
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

        foreach ($instance->getTags() as $tag) {
            $em->remove($tag);
        }

        $em->remove($instance);
        $em->flush();

        return $this->redirectToRoute('instance_index', [
            'id' => $instance->getSchema()->getId()
        ]);
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
