<?php

namespace AppBundle\Controller;


use AppBundle\DomainCommand\AddTagToInstance;
use AppBundle\DomainCommand\CreateInstance;
use AppBundle\DomainCommand\DeleteInstance;
use AppBundle\DomainCommand\EditInstance;
use AppBundle\DomainCommand\NewTag;
use AppBundle\DomainCommand\RemoveTagFromInstance;
use AppBundle\Entity\Attribute;
use AppBundle\Entity\Instance;
use AppBundle\Entity\Property;
use AppBundle\Entity\Schema;
use AppBundle\Entity\Tag;
use AppBundle\Form\TagSelectType;
use AppBundle\Form\TagType;
use AppBundle\Service\InstanceBatchAction;
use AppBundle\Service\SchemaFormFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/instance")
 */
class InstanceController extends Controller
{

    /**
     * @var InstanceBatchAction
     */
    private $iba;
    /**
     * @var SchemaFormFactory
     */
    private $sff;

    public function __construct(InstanceBatchAction $iba, SchemaFormFactory $sff)
    {
        $this->iba = $iba;
        $this->sff = $sff;
    }

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
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function batchAction(Request $request)
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
                $this->iba->assignTag($instances, $tag);
                break;
            case 'remove_tag':
                $this->iba->removeTag($instances, $tag);
                break;
            case 'delete':
                $this->iba->delete($instances);
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
     * @param Schema $schema
     * @param CreateInstance $ci
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @internal param SchemaFormFactory $sff
     */
    public function newAction(Request $request, Schema $schema, CreateInstance $ci)
    {
        $attributes = $this->fetchAttributes($schema);

        $form = $this->sff->form(
            $attributes,
            $this->generateUrl('instance_new', [
                'id' => $schema->getId()
            ])
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $instance = $this->sff->instance($form);
            $ci
                ->execute(
                    $instance,
                    $this->sff->properties($form, $instance, $attributes),
                    $schema,
                    $this->getUser()
                )
                ->flush();

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
     * @param Instance $instance
     * @param AddTagToInstance $atti
     * @param NewTag $nt
     * @param EditInstance $ei
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request,
                               Instance $instance,
                               AddTagToInstance $atti,
                               NewTag $nt,
                               EditInstance $ei)
    {
        $tagAssignForm = $this->createForm(TagSelectType::class);
        $tagCreateForm = $this->createForm(TagType::class);

        $properties = $this->getDoctrine()
            ->getRepository(Property::class)
            ->findFromInstance($instance);

        $attributes = $this->fetchAttributes($instance->getSchema());
        $editForm = $this->sff->form(
            $attributes,
            $this->generateUrl('instance_edit', [
                'id' => $instance->getId()
            ]),
            $instance,
            $properties
        );

        $routeName = false;
        if ($editForm->handleRequest($request)->isSubmitted()
            && $editForm->isValid()) {
            $instance = $this->saveEditedInstance(
                $ei,
                $instance,
                $editForm,
                $attributes,
                $properties
            );

            $routeName = 'instance_show';
        } elseif ($tagAssignForm->handleRequest($request)->isSubmitted()
            && $tagAssignForm->isValid()) {
            /** @var Tag $tag */
            $tag = $tagAssignForm->getData()['tag'];
            $atti->execute($instance, $tag);

            $routeName = 'instance_edit';
        } elseif ($tagCreateForm->handleRequest($request)->isSubmitted()
            && $tagCreateForm->isValid()) {
            /** @var Tag $tag */
            $tag = $tagCreateForm->getData();
            $nt->execute($tag, $this->getUser());
            $atti->execute($instance, $tag);

            $routeName = 'instance_edit';
        }

        if ($routeName) {
            $this->getDoctrine()
                ->getManager()
                ->flush();
            return $this->redirectToRoute($routeName, [
                'id' => $instance->getId()
            ]);
        }

        return [
            'instance' => $instance,
            'edit_form' => $editForm->createView(),
            'tag_assign_form' => $tagAssignForm->createView(),
            'tag_create_form' => $tagCreateForm->createView()
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
        $rtfi->execute($instance, $tag)->flush();

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
     * @param EditInstance $ei
     * @param Instance $instance
     * @param FormInterface $editForm
     * @param Attribute[] $attributes
     * @param Property[] $properties
     */
    public function saveEditedInstance(EditInstance $ei,
                                       Instance $instance,
                                       FormInterface $editForm,
                                       array $attributes,
                                       array $properties)
    {
        $ei->execute(
            $this->sff->instance($editForm, $instance),
            $this->sff->properties($editForm, $instance, $attributes, $properties)
        );
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
        $di->execute($instance)->flush();

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
