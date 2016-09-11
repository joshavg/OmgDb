<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/relationship")
 */
class RelationshipController extends Controller
{
    use RepositoryServices;

    /**
     * @Route("/new/chooseSchema/{instanceUid}", name="relationship_new_chooseschema")
     * @Template()
     *
     * @param $instanceUid
     * @return array
     */
    public function chooseSchemaAction($instanceUid)
    {
        $schemas = $this->getSchemaRepository()->fetchAllForCurrentUser();
        $instance = $this->getInstanceRepository()->fetchByUid($instanceUid);
        return [
            'schemas' => $schemas,
            'instance' => $instance
        ];
    }

    /**
     * @Route("/new/chooseInstance/{schemaUid}/{fromInstanceUid}",
     *     name="relationship_new_chooseinstance")
     * @Template()
     *
     * @param $schemaUid
     * @param $fromInstanceUid
     * @return array
     */
    public function chooseInstanceAction($schemaUid, $fromInstanceUid)
    {
        $instances = $this->getInstanceRepository()->fetchAllForSchema($schemaUid);
        $fromInstance = $this->getInstanceRepository()->fetchByUid($fromInstanceUid);

        return [
            'instances' => $instances,
            'fromInstance' => $fromInstance
        ];
    }

    /**
     * @Route("/new/saveRelationship", name="relationship_create")
     * @Method("POST")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveRelationshipAction(Request $request)
    {
        $postdata = $request->request;
        $fromInstanceUid = $postdata->get('fromInstance');
        $label = $postdata->get('label');
        $instanceUids = $postdata->get('instances');

        $this->getRelationshipRepository()->createRelationships($fromInstanceUid, $label,
                                                                $instanceUids);
        return $this->redirectToRoute('instance_show', [
            'uid' => $fromInstanceUid
        ]);
    }

    /**
     * @Route("/delete/{uid}/{instanceUid}", name="relationship_delete")
     *
     * @param string $uid
     * @param string $instanceUid
     * @return array
     */
    public function deleteAction($uid, $instanceUid)
    {
        $this->getRelationshipRepository()->deleteRelationship($uid);
        return $this->redirectToRoute('instance_show', [
            'uid' => $instanceUid
        ]);
    }

}
