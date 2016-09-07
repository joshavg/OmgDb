<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Architecture\RepositoryServices;
use AppBundle\Entity\Attribute;
use AppBundle\Form\Type\AttributeType;
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
