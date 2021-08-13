<?php

use Dormilich\Bundle\HttpOauthBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers \Dormilich\Bundle\HttpOauthBundle\DependencyInjection\Compiler\ProviderCompilerPass
 */
class ProviderCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ProviderCompilerPass());
    }

    /**
     * @test
     */
    public function add_provider_to_chain()
    {
        $provider = new Definition();
        $provider->addTag('dormilich_http_oauth.credentials');
        $this->setDefinition('provider', $provider);
        $this->setDefinition('dormilich_http_oauth.credentials_chain', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'dormilich_http_oauth.credentials_chain',
            'add',
            [new Reference('provider')]
        );
    }
}
