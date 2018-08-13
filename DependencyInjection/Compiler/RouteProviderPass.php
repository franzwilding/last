<?php

namespace Fw\LastBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RouteProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined.
        if (!$container->has('fw.last.route_manager')) {
            return;
        }

        $routeManager = $container->findDefinition('fw.last.route_manager');
        $taggedServices = $container->findTaggedServiceIds('fw.last.route_provider');

        foreach ($taggedServices as $id => $tags) {
            $routeManager->addMethodCall('registerRouteProvider', array(new Reference($id)));
        }
    }
}
