<?php
namespace AppBundle\Entity;

use laniger\Neo4jBundle\Architecture\Neo4jRepository;

class SchemaRepository
{
    use Neo4jRepository;

    public function newSchema(array $formdata)
    {
        $this->client->cypher('
            CREATE (n:Schema)
               SET n.name = {name}
        ', [
            'name' => $formdata['name']
        ]);
    }

    public function fetchForOverview()
    {
        return static::transformToArray($this->client->cypher('
             MATCH (n:Schema)
            RETURN n
            ORDER BY n.name
        '));
    }
}