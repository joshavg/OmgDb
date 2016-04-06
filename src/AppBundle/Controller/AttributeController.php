<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\SchemaType;
use AppBundle\Architecture\RepositoryServices;
use AppBundle\Form\Type\SchemaFilterType;
use AppBundle\Entity\Schema;
use AppBundle\Entity\Attribute;
use AppBundle\Form\Type\AttributeType;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/attribute")
 *
 * @author laniger
 */
class AttributeController extends Controller
{
    use RepositoryServices;

    /**
     * @Route("/index", name="attribute_index")
     * @Template()
     *
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $schemas = $this->getSchemaRepository()->fetchAllForCurrentUser();
        return [
            'schemas' => $schemas
        ];
    }

    /**
     * @Route("/forSchema/{schema_name}", name="attribute_for_schema")
     * @Template()
     *
     * @param $schema_name
     * @return array
     */
    public function schemaChosenAction($schema_name)
    {
        $schema = $this->getSchemaRepository()->fetch($schema_name);
        $attributes = $this->getAttributeRepository()->getForSchema($schema);

        $attr = new Attribute();
        $attr->setSchemaName($schema->getName());
        $newform = $this->createNewForm($attr);

        return [
            'schema' => $schema,
            'attributes' => $attributes,
            'newForm' => $newform->createView()
        ];
    }

    private function createNewForm(Attribute $attr)
    {
        $form = $this->createForm(AttributeType::class, $attr, [
            'action' => $this->generateUrl('attribute_insert')
        ]);
        $form->add('submit', SubmitType::class, [
            'label' => 'label.create'
        ]);
        return $form;
    }

    /**
     * @Route("/new", name="attribute_insert")
     * @Method("POST")
     * @Template("AppBundle:Attribute:schemaChosen.html.twig")
     *
     * @param Request $req
     * @return array
     */
    public function newAction(Request $req)
    {
        $attr = new Attribute();
        $form = $this->createNewForm($attr);

        if ($form->handleRequest($req)->isValid()) {
            $this->getAttributeRepository()->newAttribute($attr);
            return $this->redirectToRoute('attribute_for_schema', [
                'schema_name' => $attr->getSchemaName()
            ]);
        }

        $schema = $this->getSchemaRepository()->fetch($attr->getSchemaName());
        $attributes = $this->getAttributeRepository()->getForSchema($schema);
        return [
            'schema' => $schema,
            'attributes' => $attributes,
            'newForm' => $form->createView()
        ];
    }

    /**
     * @Route("/{schema_name}/{attribute_name}/edit", name="attribute_edit")
     * @Template()
     * @param $name
     * @return array
     */
    public function editAction($name)
    {
        $schema = $this->getSchemaRepository()->fetch($name);
        $form = $this->createForm(SchemaType::class, $schema, [
            'action' => $this->generateUrl('schema_update'),
            'goal' => 'update'
        ]);

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/update", methods={"POST", "PUT"}, name="schema_update")
     * @Template("AppBundle:Schema:edit.html.twig")
     *
     * @param Request $req
     * @return array
     */
    public function updateAction(Request $req)
    {
        $form = $this->createForm(SchemaType::class, new Schema(), [
            'goal' => 'update'
        ]);

        $form->handleRequest($req);
        if ($form->isValid()) {
            return $this->redirect($this->generateUrl('schema_index'));
        }

        return [
            'form' => $form->createView()
        ];
    }
}
