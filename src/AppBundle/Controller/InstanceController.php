<?php

namespace AppBundle\Controller;


use AppBundle\DomainCommand\AddTagToInstance;
use AppBundle\DomainCommand\DeleteInstance;
use AppBundle\DomainCommand\RemoveTagFromInstance;
use AppBundle\Entity\Attribute;
use AppBundle\Entity\Instance;
use AppBundle\Entity\Property;
use AppBundle\Entity\Schema;
use AppBundle\Entity\Tag;
use AppBundle\Form\TagSelectType;
use AppBundle\Service\InstanceBatchAction;
use AppBundle\Service\SchemaFormFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
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
            ->getRepository(Instance::class)
            ->findFromSchemaAndUser($schema, $this->getUser());

        $tags = $this->getDoctrine()
            ->getRepository(Tag::class)
            ->findFromUser($this->getUser());

        return [
            'instances' => $instances,
            'schema' => $schema,
            'tags' => $tags
        ];
    }

    /**
     * @Route("/batch", name="instance_batch")
     *
     * @param Request $request
     * @param InstanceBatchAction $iba
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function batchAction(Request $request, InstanceBatchAction $iba)
    {
        $post = $request->request;

        $tag = null;
        if ($post->has('tag') && $post->getInt('tag')) {
            $tag = $this->getDoctrine()
                ->getRepository(Tag::class)
                ->find($post->get('tag'));
        }

        $instances = [];
        $repo = $this->getDoctrine()
            ->getRepository(Instance::class);
        foreach ($post->get('instance') as $iid) {
            $instances[] = $repo->find($iid);
        }

        $action = $post->get('batch_action');
        switch ($action) {
            case 'assign_tag':
                $iba->assignTag($instances, $tag);
                break;
            case 'remove_tag':
                $iba->removeTag($instances, $tag);
                break;
            case 'delete':
                $iba->delete($instances);
                break;
            default:
                throw new \InvalidArgumentException('unknown action ' . $action);
        }

        return $this->redirectToRoute('instance_index', [
            'id' => $instances[0]->getSchema()->getId()
        ]);
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
            ->getRepository(Property::class)
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
     * @param AddTagToInstance $atti
     * @param Instance $instance
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, SchemaFormFactory $sff, AddTagToInstance $atti, Instance $instance)
    {
        $tagForm = $this->createForm(TagSelectType::class);

        $properties = $this->getDoctrine()
            ->getRepository(Property::class)
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
            /** @var Tag $tag */
            $tag = $tagForm->getData()['tag'];
            $atti->execute($instance, $tag);

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
     * @param RemoveTagFromInstance $rtfi
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeTag(Instance $instance, Tag $tag, RemoveTagFromInstance $rtfi)
    {
        $rtfi->execute($instance, $tag);

        return $this->redirectToRoute('instance_edit', [
            'id' => $instance->getId()
        ]);
    }

    /**
     * @Route("/taggedwith/{id}", name="instance_tagged_with")
     * @Template
     *
     * @param Tag $tag
     * @return array
     */
    public function taggedWithAction(Tag $tag)
    {
        $instances = $this->getDoctrine()
            ->getRepository(Instance::class)
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
     * @param DeleteInstance $di
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Instance $instance, DeleteInstance $di)
    {
        $di->execute($instance);

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
            ->getRepository(Attribute::class)
            ->findFromSchema($schema);
    }

}
