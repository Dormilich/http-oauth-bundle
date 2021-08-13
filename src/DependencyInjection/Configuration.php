<?php

namespace Dormilich\Bundle\HttpOauthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $default = $this->getDefaultSection();
        $domain = $this->getDomainSection();

        $rootNode = $this->createNode('dormilich_http_client');
        $rootNode
            ->children()
                ->arrayNode('credentials')
                    ->fixXmlConfig('domain')
                    ->children()
                        ->append($default)
                        ->append($domain)
                    ->end()
                ->end()
            ->end()
        ;

        return $rootNode->end();
    }

    private function createNode(string $name)
    {
        $treeBuilder = new TreeBuilder($name);
        return $treeBuilder->getRootNode();
    }

    public function getDefaultSection()
    {
        $node = $this->createNode('default');

        $node
            ->children()
                ->scalarNode('client')
                    ->info('OAuth client id')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('secret')
                    ->info('OAuth client secret')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('server')
                    ->info('OAuth authorisation server')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;
        return $node;
    }

    public function getDomainSection()
    {
        $node = $this->createNode('domains');

        $node
            ->arrayPrototype()
                ->fixXmlConfig('host')
                ->children()
                    ->scalarNode('client')
                        ->info('OAuth client id')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('secret')
                        ->info('OAuth client secret')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('server')
                        ->info('OAuth authorisation server')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('hosts')
                        ->beforeNormalization()
                            ->castToArray()
                        ->end()
                        ->scalarPrototype()
                            ->info('Domains for which the credentials should be used')
                            ->cannotBeEmpty()
                        ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
