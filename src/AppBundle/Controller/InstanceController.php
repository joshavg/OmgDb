<?php

namespace AppBundle\Controller;

use AppBundle\Architecture\RepositoryServices;
use AppBundle\Entity\Instance;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/instance")
 */
class InstanceController extends Controller
{
    use RepositoryServices;

    /**
     * @Route("/{schema_uid}", name="instance_overview")
     * @Template()
     *
     * @param $schema_uid
     * @return array
     */
    public function indexAction($schema_uid)
    {
        $instance = $this->createEmptyInstance($schema_uid);

        $factory = $this->get('factory.instance_form');
        $form = $factory->createForm($instance, $this->generateUrl('instance_new', [
            'schema_uid' => $schema_uid
        ]));

        return [
            'form' => $form->createView(),
            'schema' => $instance->getSchema()
        ];
    }

    /**
     * @param $schema_uid
     * @return Instance
     */
    private function createEmptyInstance($schema_uid)
    {
        $schema = $this->getSchemaRepository()->fetchByUid($schema_uid);
        $attrs = $this->getAttributeRepository()->getForSchema($schema);

        $instance = new Instance();
        return $instance->setSchema($schema)->setAttributes($attrs);
    }

    /**
     * @Route("/new/{schema_uid}", name="instance_new")
     * @Template("@App/Instance/index.html.twig")
     *
     * @param string $schema_uid
     * @param Request $req
     * @return array
     */
    public function newInstanceAction($schema_uid, Request $req)
    {
        $instance = $this->createEmptyInstance($schema_uid);
        $form = $this->get('factory.instance_form')
            ->createForm($instance, $this->generateUrl('instance_new', [
                'schema_uid' => $schema_uid
            ]));

        if ($form->handleRequest($req)->isValid()) {
            // save instance
            // redirect
        }

        return [
            'form' => $form->createView(),
            'schema' => $instance->getSchema()
        ];
    }
}
