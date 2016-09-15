<?php

namespace AppBundle\Controller;

use AppBundle\Architecture\ContainerServices;
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
    use RepositoryServices, ContainerServices;

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
            'schemaUid' => $schema_uid
        ]));

        return [
            'form' => $form->createView(),
            'schema' => $this->getSchemaRepository()->fetchByUid($instance->getSchemaUid()),
            'instances' => $this->getInstanceRepository()->fetchAllForSchema($schema_uid)
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
     * @Route("/new/{schemaUid}", name="instance_new")
     * @Template("@App/Instance/index.html.twig")
     *
     * @param string $schemaUid
     * @param Request $req
     * @return array
     */
    public function newInstanceAction($schemaUid, Request $req)
    {
        $instance = $this->createEmptyInstance($schemaUid);
        $form = $this->get('factory.instance_form')
                     ->createForm($instance, $this->generateUrl('instance_new', [
                         'schemaUid' => $schemaUid
                     ]));

        if ($form->handleRequest($req)->isValid()) {
            $instance = $this->get('factory.instance')->createFromDataArray($form->getData());
            $this->getInstanceRepository()->newInstance($instance);

            $this->getFlashbagHandler()->addSaveSuccess();
            return $this->redirectToRoute('instance_overview', [
                'schema_uid' => $schemaUid
            ]);
        }

        return [
            'form' => $form->createView(),
            'schema' => $this->getSchemaRepository()->fetchByUid($instance->getSchemaUid()),
            'instances' => $this->getInstanceRepository()->fetchAllForSchema($schemaUid)
        ];
    }

    /**
     * @Route("/show/{uid}", name="instance_show")
     * @Template()
     *
     * @param string $uid
     * @return array
     */
    public function showAction($uid)
    {
        $instance = $this->getInstanceRepository()->fetchByUid($uid);
        $rels = $this->getRelationshipRepository()->getRelationships($instance);

        $schema = $this->getSchemaRepository()->fetchByUid($instance->getSchemaUid());

        $form = $this->get('factory.instance_form')
                     ->createForm($instance, $this->generateUrl('instance_update', [
                         'uid' => $uid
                     ]));

        $duplicate = $this->get('factory.instance')->createDuplicate($instance);
        $copyform = $this->get('factory.instance_form')
                         ->createForm($duplicate, $this->generateUrl('instance_new', [
                             'schemaUid' => $instance->getSchemaUid()
                         ]));

        return [
            'instance' => $instance,
            'schema' => $schema,
            'form' => $form->createView(),
            'copyform' => $copyform->createView(),
            'relationships' => $rels
        ];
    }

    /**
     * @Route("/update/{uid}", name="instance_update")
     * @Template("@App/Instance/show.html.twig")
     *
     * @param string $uid
     * @param Request $req
     * @return mixed
     */
    public function updateAction($uid, Request $req)
    {
        $instance = $this->getInstanceRepository()->fetchByUid($uid);
        $form = $this->get('factory.instance_form')
                     ->createForm($instance, $this->generateUrl('instance_update', [
                         'uid' => $uid
                     ]));

        if ($form->handleRequest($req)->isValid()) {
            $instance = $this->get('factory.instance')->createFromDataArray($form->getData());
            $this->getInstanceRepository()->update($instance);

            $this->getFlashbagHandler()->addSaveSuccess();
            return $this->redirectToRoute('instance_show', [
                'uid' => $uid
            ]);
        }

        return $this->showAction($uid);
    }

    /**
     * @Route("/delete/{uid}", name="instance_delete")
     *
     * @param $uid
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($uid)
    {
        $instance = $this->getInstanceRepository()->fetchByUid($uid);
        $this->getInstanceRepository()->deleteByUid($uid);

        $this->getFlashbagHandler()->addDeleteSuccess();
        return $this->redirectToRoute('instance_overview', [
            'schema_uid' => $instance->getSchemaUid()
        ]);
    }

    /**
     * @Route("/starred/{uid}/{starred}", name="instance_starred")
     *
     * @param $uid
     * @param $starred
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function starAction($uid, $starred)
    {
        $this->getInstanceRepository()->updateStarred($uid, $starred == 'true');

        $this->getFlashbagHandler()->addSaveSuccess();
        return $this->redirectToRoute('instance_show', [
            'uid' => $uid
        ]);
    }
}
