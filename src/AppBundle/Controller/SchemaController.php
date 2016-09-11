<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\SchemaType;
use AppBundle\Entity\Schema;

/**
 * @Route("/schema")
 */
class SchemaController extends Controller
{
    use RepositoryServices;

    /**
     * @Route("/new", methods={"POST"}, name="schema_insert")
     * @Template("AppBundle:Schema:new.html.twig")
     *
     * @param Request $request
     * @return mixed
     */
    public function insertSchemaAction(Request $request)
    {
        $form = $this->createForm(SchemaType::class, new Schema());
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
        $dat = $this->getSchemaRepository()->fetchAllForCurrentUser();
        $form = $this->createForm(SchemaType::class, new Schema(), [
            'action' => $this->generateUrl('schema_insert')
        ]);
        return [
            'schemas' => $dat,
            'newform' => $form->createView()
        ];
    }

    /**
     * @Route("/{uid}/edit", name="schema_edit")
     * @Template()
     *
     * @param string $uid
     * @return array
     */
    public function editAction($uid)
    {
        $schema = $this->getSchemaRepository()->fetchByUid($uid);
        $form = $this->createForm(SchemaType::class, $schema, [
            'action' => $this->generateUrl('schema_update', ['uid' => $uid]),
            'validation_groups' => ['update']
        ]);

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/{uid}/update", methods={"POST", "PUT"}, name="schema_update")
     * @Template("AppBundle:Schema:edit.html.twig")
     *
     * @param string $uid
     * @param Request $req
     * @return mixed
     */
    public function updateAction($uid, Request $req)
    {
        $schema = $this->getSchemaRepository()->fetchByUid($uid);
        $form = $this->createForm(SchemaType::class, $schema, [
            'validation_groups' => ['update']
        ]);

        $form->handleRequest($req);
        if ($form->isValid()) {
            return $this->redirect($this->generateUrl('schema_index'));
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/{uid}/delete", methods={"GET", "DELETE"}, name="schema_delete")
     *
     * @param $uid
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($uid)
    {
        $this->getSchemaRepository()->deleteByUid($uid);
        return $this->redirectToRoute('schema_index');
    }
}
