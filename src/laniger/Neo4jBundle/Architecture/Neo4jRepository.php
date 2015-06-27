<?php
namespace laniger\Neo4jBundle\Architecture;

trait Neo4jRepository
{
    use Neo4jClientConsumer;

    private static function transformToArray($data = null, $name = 'n')
    {
        $return = [];
        foreach($data as $dat) {
            $return[] = $dat[$name];
        }
        return $return;
    }
}