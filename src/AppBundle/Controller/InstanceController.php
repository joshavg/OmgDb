<?php

namespace AppBundle\Controller;

use AppBundle\Architecture\RepositoryServices;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $schema = $this->getSchemaRepository()->fetchByUid($schema_uid);
        $attrs = $this->getAttributeRepository()->getForSchema($schema);

        $factory = $this->get('factory.instance_form');
        $form = $factory->createForm($schema, $attrs);

        return [
            'form' => $form->createView(),
            'schema' => $schema
        ];
    }
}
