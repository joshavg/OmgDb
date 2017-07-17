<?php

namespace AppBundle\Controller;


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
     * @Route("/{id}", name="instance_index")
     * @Template
     *
     * @param Schema $schema
     * @return array
     */
    public function indexAction(Schema $schema)
    {
        $instances = $this->getDoctrine()
            ->getRepository('AppBundle:Instance')
            ->findBy([
                'schema' => $schema
            ], [
                'createdAt' => 'ASC'
            ]);

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
     * @return array
     */
    public function newAction(Request $request, SchemaFormFactory $sff, Schema $schema)
    {
        $attributes = $this->getDoctrine()
            ->getRepository('AppBundle:Attribute')
            ->findBy([
                'schema' => $schema
            ]);

        $form = $sff->build($attributes);
        $form->handleRequest($request);

//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($attribute);
//            $em->flush();
//
//            return $this->redirectToRoute('attribute_show',
//                ['id' => $attribute->getId()]);
//        }

        return [
            'schema' => $schema,
            'form' => $form->createView()
        ];
    }

}
