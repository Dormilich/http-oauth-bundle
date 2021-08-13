<?php

use Dormilich\Bundle\HttpOauthBundle\DependencyInjection\DormilichHttpOauthExtension;
use Dormilich\HttpOauth\Credentials\ChainProvider;
use Dormilich\HttpOauth\Credentials\CredentialsProviderInterface;
use Dormilich\HttpOauth\Credentials\DefaultProvider;
use Dormilich\HttpOauth\Credentials\DomainProvider;
use Dormilich\HttpOauth\Encoder\AuthorisationEncoder;
use Dormilich\HttpOauth\TokenClient;
use Dormilich\HttpOauth\TokenClientInterface;
use Dormilich\HttpOauth\TokenProvider;
use Dormilich\HttpOauth\TokenProviderInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers \Dormilich\Bundle\HttpOauthBundle\DependencyInjection\DormilichHttpOauthExtension
 */
class DormilichHttpOauthExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @inheritDoc
     */
    protected function getContainerExtensions(): array
    {
        return [new DormilichHttpOauthExtension()];
    }

    /**
     * @test
     */
    public function load_encoder_components()
    {
        $this->load();

        $this->assertContainerBuilderHasService('dormilich_http_oauth.encoder', AuthorisationEncoder::class);
        $this->assertContainerBuilderHasService('dormilich_http_oauth.token_provider', TokenProvider::class);
        $this->assertContainerBuilderHasService('dormilich_http_oauth.token_client', TokenClient::class);

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'dormilich_http_oauth.encoder',
            'dormilich_http_client.client_encoder'
        );
    }

    /**
     * @test
     */
    public function load_credentials_providers()
    {
        $this->load();

        $this->assertContainerBuilderHasService('dormilich_http_oauth.credentials_chain', ChainProvider::class);
        $this->assertContainerBuilderHasService('dormilich_http_oauth.credentials_default', DefaultProvider::class);
        $this->assertContainerBuilderHasService('dormilich_http_oauth.credentials_domain', DomainProvider::class);
    }

    /**
     * @test
     */
    public function load_classname_aliases()
    {
        $this->load();

        $this->assertContainerBuilderHasAlias(AuthorisationEncoder::class, 'dormilich_http_oauth.encoder');
    }

    /**
     * @test
     */
    public function load_interface_aliases()
    {
        $this->load();

        $this->assertContainerBuilderHasAlias(TokenProviderInterface::class, 'dormilich_http_oauth.token_provider');
        $this->assertContainerBuilderHasAlias(TokenClientInterface::class, 'dormilich_http_oauth.token_client');
        $this->assertContainerBuilderHasAlias(CredentialsProviderInterface::class, 'dormilich_http_oauth.credentials_chain');
    }

    /**
     * @test
     */
    public function configure_default_credentials()
    {
        $credentials['client'] = 'test';
        $credentials['secret'] = '8582992d-ddd8-4a9d-be4b-60b39e487b62';
        $credentials['server'] = 'https://example.com/oauth/token';
        $config['credentials']['default'] = $credentials;
        $this->load($config);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'dormilich_http_oauth.credentials_default',
            0
        );
        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'dormilich_http_oauth.credentials_default',
            'dormilich_http_oauth.credentials'
        );
    }

    /**
     * @test
     */
    public function configure_domain_credentials()
    {
        $credentials['client'] = 'test';
        $credentials['secret'] = '8582992d-ddd8-4a9d-be4b-60b39e487b62';
        $credentials['server'] = 'https://example.com/oauth/token';
        $credentials['hosts'][0] = 'example.org';
        $config['credentials']['domains'][0] = $credentials;
        $this->load($config);

        $arguments[] = new Reference('dormilich_http_oauth.domain_0');
        $arguments[] = ['example.org'];
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'dormilich_http_oauth.credentials_domain',
            'add',
            $arguments,
            0
        );
        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'dormilich_http_oauth.credentials_domain',
            'dormilich_http_oauth.credentials'
        );
    }
}
