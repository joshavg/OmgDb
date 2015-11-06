<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\SchemaType;
use AppBundle\Architecture\RepositoryServices;
use AppBundle\Form\FormDefinition;
use AppBundle\Form\Type\SchemaFilterType;
use AppBundle\Entity\Schema;
use AppBundle\Entity\Attribute;
use AppBundle\Form\Type\AttributeType;

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
     * @return array
     */
    public function indexAction(Request $request)
    {
        $schemas = $this->getSchemaRepository()->fetchForOverview();
        $schemaFilterForm = $this->createForm(new SchemaFilterType($schemas), []);
        $schemaFilterForm->add('filter', SubmitType::class, [
            'label' => 'label.attribute.filter'
        ]);

        $attributes = [];
        $newform = null;
        if($schemaFilterForm->handleRequest($request)->isValid()) {
            $schemaname = $schemaFilterForm->getData();
            $schema = $this->getSchemaRepository()->fetch($schemaname['schema']);

            $attributes = $this->getAttributeRepository()->getForSchema($schema);

            $attr = new Attribute();
            $attr->setSchema($schema);
            $newform = $this->createNewForm($attr);
        }

        return [
            'attributes' => $attributes,
            'schemaFilterForm' => $schemaFilterForm->createView(),
            'newForm' => $newform ? $newform->createView() : null
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
     */
    public function newAction(Request $req)
    {
        return [];
    }

    /**
     * @Route("/{schema_name}/{attribute_name}/edit", name="attribute_edit")
     * @Template()
     */
    public function editAction($name)
    {
        $schema = $this->getSchemaRepository()->fetch($name);
        $form = $this->createForm(new SchemaType(FormDefinition::MODE_EDIT), $schema, [
            'action' => $this->generateUrl('schema_update')
        ]);

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/update", methods={"POST", "PUT"}, name="schema_update")
     * @Template("AppBundle:Schema:edit.html.twig")
     */
    public function updateAction(Request $req)
    {
        $form = $this->createForm(new SchemaType(FormDefinition::MODE_EDIT), new Schema());

        $form->handleRequest($req);
        if ($form->isValid()) {
            return $this->redirect($this->generateUrl('schema_index'));
        }

        return [
            'form' => $form->createView()
        ];
    }
}
