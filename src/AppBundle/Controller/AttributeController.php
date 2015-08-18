<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\SchemaType;
use AppBundle\Architecture\RepositoryServices;
use AppBundle\Form\FormDefinition;
use AppBundle\Form\SchemaFilterType;
use AppBundle\Entity\Schema;

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
        $schemaFilterForm->add('filter', 'submit', [
            'label' => 'label.attribute.filter'
        ]);
        
        $attributes = [];
        if($schemaFilterForm->handleRequest($request)->isValid()) {
            $schemaname = $schemaFilterForm->getData();
            $schema = $this->getSchemaRepository()->fetch($schemaname['schema']);
            
            $attributes = $this->getAttributeRepository()->getForSchema($schema);
        }
        
        return [
            'attributes' => $attributes,
            'schemaFilterForm' => $schemaFilterForm->createView()
        ];
    }

    /**
     * @Route("/{name}/edit", name="schema_edit")
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
