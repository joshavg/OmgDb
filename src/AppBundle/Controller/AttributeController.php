<?php
namespace AppBundle\Controller;

use AppBundle\Architecture\ContainerServices;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Attribute;
use AppBundle\Form\Type\AttributeType;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/attribute")
 */
class AttributeController extends Controller
{
    use RepositoryServices, ContainerServices;

    /**
     * @Route("/index", name="attribute_index")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        $schemas = $this->getSchemaRepository()->fetchAllForCurrentUser();
        return [
            'schemas' => $schemas
        ];
    }

    /**
     * @Route("/forSchema/{uid}", name="attribute_for_schema")
     * @Template()
     *
     * @param $uid
     * @return array
     */
    public function schemaChosenAction($uid)
    {
        $schema = $this->getSchemaRepository()->fetchByUid($uid);
        $attributes = $this->getAttributeRepository()->getForSchema($schema);

        $attr = new Attribute();
        $attr->setSchemaUid($schema->getUid());
        $attr->setSchemaName($schema->getName());
        $newform = $this->createNewForm($attr);

        return [
            'schema' => $schema,
            'attributes' => $attributes,
            'newForm' => $newform->createView()
        ];
    }

    private function createNewForm(Attribute $attr)
    {
        $form = $this->createForm(AttributeType::class, $attr, [
            'action' => $this->generateUrl('attribute_insert')
        ]);
        $form->add('submit', SubmitType::class, [
            'label' => 'label.create'
        ]);
        return $form;
    }

    /**
     * @Route("/new", name="attribute_insert")
     * @Method("POST")
     * @Template("AppBundle:Attribute:schemaChosen.html.twig")
     *
     * @param Request $req
     * @return array
     */
    public function newAction(Request $req)
    {
        $attr = new Attribute();
        $form = $this->createNewForm($attr);

        if ($form->handleRequest($req)->isValid()) {
            $this->getAttributeRepository()->newAttribute($attr);

            $this->getFlashbagHandler()->addSaveSuccess();
            return $this->redirectToRoute('attribute_for_schema', [
                'uid' => $attr->getSchemaUid()
            ]);
        }

        $schema = $this->getSchemaRepository()->fetchByUid($attr->getSchemaUid());
        $attributes = $this->getAttributeRepository()->getForSchema($schema);
        return [
            'schema' => $schema,
            'attributes' => $attributes,
            'newForm' => $form->createView()
        ];
    }

    /**
     * @Route("/{uid}/edit", name="attribute_edit")
     * @Template()
     *
     * @param string $uid
     * @return array
     */
    public function editAction($uid)
    {
        $attr = $this->getAttributeRepository()->fetchByUid($uid);
        $form = $this->createEditForm($attr);

        return [
            'form' => $form->createView()
        ];
    }

    private function createEditForm(Attribute $attr)
    {
        $form = $this->createForm(AttributeType::class, $attr, [
            'action' => $this->generateUrl('attribute_update', [
                'uid' => $attr->getUid(),
            ]),
            'validation_groups' => ['update']
        ]);
        $form->add('submit', SubmitType::class, [
            'label' => 'label.save'
        ]);
        return $form;
    }

    /**
     * @Route("/{uid}/update", methods={"POST", "PUT"}, name="attribute_update")
     * @Template("AppBundle:Schema:edit.html.twig")
     *
     * @param string $uid
     * @param Request $req
     * @return array
     */
    public function updateAction($uid, Request $req)
    {
        $attr = $this->getAttributeRepository()->fetchByUid($uid);
        $form = $this->createEditForm($attr);

        if ($form->handleRequest($req)->isValid()) {
            $this->getAttributeRepository()->update($uid, $attr);

            $this->getFlashbagHandler()->addSaveSuccess();
            return $this->redirectToRoute('attribute_for_schema', [
                'uid' => $attr->getSchemaUid()
            ]);
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/{uid}/delete", methods={"GET", "DELETE"}, name="attribute_delete")
     *
     * @param $uid
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($uid)
    {
        $attr = $this->getAttributeRepository()->fetchByUid($uid);
        $this->getAttributeRepository()->deleteByUid($uid);

        $this->getFlashbagHandler()->addDeleteSuccess();
        return $this->redirectToRoute('attribute_for_schema', [
            'uid' => $attr->getSchemaUid()
        ]);
    }
}
