<?php

namespace AppBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PropertyTransformerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('transformer.property.repo')) {
            return;
        }

        $definition = $container->findDefinition('transformer.property.repo');
        $taggedServices = $container->findTaggedServiceIds('transformer.property');

        foreach ($taggedServices as $id => $tags) {
            // add the transport service to the ChainTransport service
            $definition->addMethodCall('addTransformer', [
                $id,
                new Reference($id)
            ]);
        }
    }
}
