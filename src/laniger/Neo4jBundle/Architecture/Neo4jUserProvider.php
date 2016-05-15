<?php
namespace laniger\Neo4jBundle\Architecture;

use GraphAware\Neo4j\Client\Formatter\RecordView;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class Neo4jUserProvider implements UserProviderInterface
{

    /**
     * @var Neo4jClientWrapper
     */
    private $client;

    public function __construct(Neo4jClientWrapper $client)
    {
        $this->client = $client;
    }

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserProviderInterface::loadUserByUsername()
     */
    public function loadUserByUsername($username)
    {
        $res = $this->client->cypher('MATCH (n:user) WHERE n.name = {name} RETURN n', [
            'name' => $username
        ]);

        /** @var $rows RecordView */
        $rows = $res->records();

        if (! count($rows)) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        $row = $rows[0]->get('n');
        $user = new User();
        $user->setName($row->get('name'));
        $user->setEmail($row->get('email'));
        $user->setPassword($row->get('password'));
        return $user;
    }

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserProviderInterface::refreshUser()
     */
    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserProviderInterface::supportsClass()
     */
    public function supportsClass($class)
    {
        return $class === User::class;
    }
}
