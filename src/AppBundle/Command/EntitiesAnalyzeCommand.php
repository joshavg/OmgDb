<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * generates a graphml file from the known entities
 * @author jgizycki
 */
class EntitiesAnalyzeCommand extends ContainerAwareCommand
{

    /**
     * the recognized entites with 'name' => ['props' => [ name ], 'id' => int ]
     * @var array
     */
    private $entities;

    /**
     * the known edges with [ 'from' => name, 'to' => name ]
     * @var array
     */
    private $edges;

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('entities:analyze');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->analyzeEntities();
        file_put_contents('out.graphml', $this->generateXml());
    }

    /**
     * generates the xml with $entites and $edges
     * @return string
     */
    private function generateXml()
    {
        $out = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
        $out .= '<graphml xmlns="http://graphml.graphdrawing.org/xmlns" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:y="http://www.yworks.com/xml/graphml" xmlns:yed="http://www.yworks.com/xml/yed/3" xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns http://www.yworks.com/xml/schema/graphml/1.1/ygraphml.xsd">';
        $out .= <<<DOC
        <key attr.name="Beschreibung" attr.type="string" for="graph" id="d0"/>
  <key for="port" id="d1" yfiles.type="portgraphics"/>
  <key for="port" id="d2" yfiles.type="portgeometry"/>
  <key for="port" id="d3" yfiles.type="portuserdata"/>
  <key attr.name="url" attr.type="string" for="node" id="d4"/>
  <key attr.name="description" attr.type="string" for="node" id="d5"/>
  <key for="node" id="d6" yfiles.type="nodegraphics"/>
  <key for="graphml" id="d7" yfiles.type="resources"/>
  <key attr.name="url" attr.type="string" for="edge" id="d8"/>
  <key attr.name="description" attr.type="string" for="edge" id="d9"/>
  <key for="edge" id="d10" yfiles.type="edgegraphics"/>
DOC;
        $out .= '<graph edgedefault="directed" id="G">';

        $i = 0;
        foreach ($this->entities as $name => $entity) {
            $i++;
            $this->entities[$name]['id'] = $i;
            $out .= '<node id="n' . $i . '">';
            $out .= '<data key="d6">';

            $props = '';
            $propsheight = 1;
            if (isset($entity['props'])) {
                foreach ($entity['props'] as $prop) {
                    $props .= $prop . "\n";
                    $propsheight += 16.8;
                }
                $props = trim($props);
            }

            $height = $propsheight + 17.9;
            $sname = $entity['sname'];
            $out .= <<< DOC
<y:GenericNode configuration="com.yworks.entityRelationship.big_entity">
  <y:Geometry height="{$height}" width="107.0"/>
  <y:Fill color="#E8EEF7" color2="#B7C9E3" transparent="false"/>
  <y:BorderStyle color="#000000" type="line" width="1.0"/>
  <y:NodeLabel alignment="center" autoSizePolicy="content" backgroundColor="#B7C9E3" configuration="com.yworks.entityRelationship.label.name" fontFamily="Dialog" fontSize="12" fontStyle="plain" hasLineColor="false" height="17.9" modelName="internal" modelPosition="t" textColor="#000000" visible="true" width="34.978515625" x="36.0107421875" y="4.0">{$sname}</y:NodeLabel>
  <y:NodeLabel alignment="left" autoSizePolicy="content" configuration="com.yworks.entityRelationship.label.attributes" fontFamily="Dialog" fontSize="12" fontStyle="plain" hasBackgroundColor="false" hasLineColor="false" height="{$propsheight}" modelName="custom" textColor="#000000" visible="true" width="68.634765625" x="2.0" y="29.96875">{$props}<y:LabelModel>
      <y:ErdAttributesNodeLabelModel/>
    </y:LabelModel>
    <y:ModelParameter>
      <y:ErdAttributesNodeLabelModelParameter/>
    </y:ModelParameter>
  </y:NodeLabel>
  <y:StyleProperties>
    <y:Property class="java.lang.Boolean" name="y.view.ShadowNodePainter.SHADOW_PAINTING" value="true"/>
  </y:StyleProperties>
</y:GenericNode>
DOC;
            $out .= '</data>';
            $out .= '</node>';
        }

        $i = 0;
        foreach ($this->edges as $edge) {
            $i++;
            $idfrom = $this->entities[$edge['from']]['id'];
            $idto = $this->entities[$edge['to']]['id'];
            $out .= <<<DOC
    <edge id="e{$i}" source="n{$idfrom}" target="n{$idto}">
      <data key="d9"/>
    </edge>
DOC;
        }

        $out .= '</graph>';
        return $out . '</graphml>';
    }

    /**
     * analyzes the entity directory
     */
    private function analyzeEntities()
    {
        $dir = realpath(__DIR__ . '/../Entity');
        $it = new \DirectoryIterator($dir);

        foreach ($it as $file) {
            if ($file->isDot() || $file->isDir()) {
                continue;
            }

            $entityname = $file->getBasename('.php');

            $fqcn = 'AppBundle\\Entity\\' . $entityname;
            $refclass = new \ReflectionClass($fqcn);
            $this->processEntity($refclass, $fqcn);
        }
    }

    /**
     * analyzes the properties of the given entity
     * @param \ReflectionClass $refclass
     * @param string $fqcn
     */
    private function processEntity(\ReflectionClass $refclass, $fqcn)
    {
        if (!isset($this->entities[$fqcn])) {
            $this->entities[$fqcn] = [
                'props' => [],
                'refs' => [],
                'sname' => $refclass->getShortName()
            ];
        }

        foreach ($refclass->getProperties() as $propinfo) {
            $propname = $propinfo->getName();
            $prop = $refclass->getProperty($propname);
            $comment = $prop->getDocComment();

            if (strpos($comment, '@ORM') !== false) {
                $this->entities[$fqcn]['props'][] = $propname;
            }

            $matches = [];
            if (preg_match('/ManyToOne\(targetEntity="([^"]+)"/', $comment, $matches)) {
                $this->edges[] = [
                    'from' => $fqcn,
                    'to' => $matches[1]
                ];
                $this->entities[$fqcn]['refs'][] = $matches[1];
            }
        }
    }
}
