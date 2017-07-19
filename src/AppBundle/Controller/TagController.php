<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tag controller.
 *
 * @Route("Tag")
 */
class TagController extends Controller
{
    /**
     * Lists all Tag entities.
     *
     * @Route("/", name="tag_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tags = $em
            ->getRepository('AppBundle:Tag')
            ->findFromUser($this->getUser());

        return [
            'tags' => $tags,
        ];
    }

    /**
     * Creates a new Tag entity.
     *
     * @Route("/new", name="tag_new")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request)
    {
        $tag = new Tag();
        $form = $this->createForm('AppBundle\Form\TagType', $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $tag->setCreatedBy($this->getUser());
            $em->persist($tag);
            $em->flush();

            return $this->redirectToRoute('tag_show',
                ['id' => $tag->getId()]);
        }

        return [
            'tag' => $tag,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Tag entity.
     *
     * @Route("/{id}", name="tag_show")
     * @Method("GET")
     * @Template
     */
    public function showAction(Tag $tag)
    {
        $this->getDoctrine()
            ->getRepository('AppBundle:Tag')
            ->assertBelongsToUser($tag, $this->getUser());

        $deleteForm = $this->createDeleteForm($tag);

        return [
            'tag' => $tag,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing Tag entity.
     *
     * @Route("/{id}/edit", name="tag_edit")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function editAction(Request $request, Tag $tag)
    {
        $this->getDoctrine()
            ->getRepository('AppBundle:Tag')
            ->assertBelongsToUser($tag, $this->getUser());

        $deleteForm = $this->createDeleteForm($tag);
        $editForm = $this->createForm('AppBundle\Form\TagType', $tag);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tag_index');
        }

        return [
            'tag' => $tag,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a Tag entity.
     *
     * @Route("/{id}", name="tag_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Tag $tag)
    {
        $this->getDoctrine()
            ->getRepository('AppBundle:Tag')
            ->assertBelongsToUser($tag, $this->getUser());

        $form = $this->createDeleteForm($tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tag);
            $em->flush();
        }

        return $this->redirectToRoute('tag_index');
    }

    /**
     * Creates a form to delete a Tag entity.
     *
     * @param Tag $tag The Tag entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Tag $tag)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tag_delete', array('id' => $tag->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
