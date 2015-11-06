<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Architecture\ContainerServices;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\SchemaType;
use AppBundle\Entity\Schema;

class DefaultController extends Controller
{
    use ContainerServices;

    /**
     * @Route("/", name="index")
     * @Template
     */
    public function indexAction(Request $req)
    {
        $form = $this->createForm(SchemaType::class, new Schema(), [
            'action' => $this->generateUrl('schema_insert')
        ]);

        $form->handleRequest($req);
        $form->isValid();

        return [
            'newSchemaForm' => $form->createView()
        ];
    }
}
