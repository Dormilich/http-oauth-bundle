<?php

namespace Dormilich\Bundle\HttpOauthBundle\DependencyInjection;

use Dormilich\HttpOauth\Credentials\ClientCredentials;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class DormilichHttpOauthExtension extends ConfigurableExtension
{
    /**
     * @inheritDoc
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $this->loadDefinitions($container);

        if (!empty($mergedConfig['credentials']['default'])) {
            $this->loadDefaultCredentials($mergedConfig['credentials']['default'], $container);
        }
        if (!empty($mergedConfig['credentials']['domains'])) {
            $this->loadDomainCredentials($mergedConfig['credentials']['domains'], $container);
        }
    }

    private function loadDefinitions(ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.xml');
    }

    private function loadDefaultCredentials(array $config, ContainerBuilder $container)
    {
        $credentials = $this->createCredentials('default', $config, $container);

        $provider = $container->findDefinition('dormilich_http_oauth.credentials_default');
        $provider->setArgument(0, $credentials);
        $provider->addTag('dormilich_http_oauth.credentials');
    }

    private function loadDomainCredentials(array $config, ContainerBuilder $container)
    {
        $provider = $container->findDefinition('dormilich_http_oauth.credentials_domain');
        $provider->addTag('dormilich_http_oauth.credentials');

        foreach ($config as $index => $item) {
            $provider->addMethodCall('add', [
                $this->createCredentials('domain_' . $index, $item, $container),
                $item['hosts']
            ]);
        }
    }

    private function createCredentials(string $name, array $config, ContainerBuilder $container): Reference
    {
        $arguments[] = $config['client'];
        $arguments[] = $config['secret'];
        $arguments[] = $config['server'];
        $definition = new Definition(ClientCredentials::class, $arguments);

        $service = 'dormilich_http_oauth.' . $name;
        $container->setDefinition($service, $definition);
        return new Reference($service);
    }
}
