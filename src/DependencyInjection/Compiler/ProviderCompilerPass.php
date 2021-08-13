<?php

namespace Dormilich\Bundle\HttpOauthBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ProviderCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('dormilich_http_oauth.credentials_chain')) {
            return;
        }

        $definition = $container->findDefinition('dormilich_http_oauth.credentials_chain');
        $taggedServices = $container->findTaggedServiceIds('dormilich_http_oauth.credentials');

        foreach ($taggedServices as $id => $attr) {
            $definition->addMethodCall('add', [new Reference($id)]);
        }
    }
}
