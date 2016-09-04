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

        $schema = $this->getSchemaRepository()->fetchByUid($schema_uid);
        return [
            'form' => $form->createView(),
            'schema' => $this->getSchemaRepository()->fetchByUid($instance->getSchemaUid()),
            'instances' => $this->getInstanceRepository()->fetchAllForSchema($schema)
        ];
    }

    /**
     * @param $schema_uid
     * @return Instance
     */
    private function createEmptyInstance($schema_uid)
    {
        return $this->get('factory.instance')->createEmptyInstance($schema_uid);
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
            $instance = $this->get('factory.instance')->createFromDataArray($form->getData());
            $this->getInstanceRepository()->newInstance($instance);

            return $this->redirectToRoute('instance_overview', [
                'schema_uid' => $schema_uid
            ]);
        }

        $schema = $this->getSchemaRepository()->fetchByUid($schema_uid);
        return [
            'form' => $form->createView(),
            'schema' => $this->getSchemaRepository()->fetchByUid($instance->getSchemaUid()),
            'instances' => $this->getInstanceRepository()->fetchAllForSchema($schema)
        ];
    }

    /**
     * @Route("/show/{schema_uid}", name="instance_show")
     * @Template()
     *
     * @param string $instance_uid
     */
    public function showAction($instance_uid)
    {
        $this->getInstanceRepository()->fetchByUid($instance_uid);
    }
}
