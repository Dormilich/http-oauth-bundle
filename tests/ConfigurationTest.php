<?php


use Dormilich\Bundle\HttpOauthBundle\DependencyInjection\Configuration;
use Dormilich\Bundle\HttpOauthBundle\DependencyInjection\DormilichHttpOauthExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @covers \Dormilich\Bundle\HttpOauthBundle\DependencyInjection\Configuration
 */
class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    /**
     * @inheritDoc
     */
    protected function getContainerExtension(): ExtensionInterface
    {
        return new DormilichHttpOauthExtension();
    }

    /**
     * @inheritDoc
     */
    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }

    /**
     * @test
     */
    public function load_default_credentials()
    {
        $credentials['client'] = 'test';
        $credentials['secret'] = '8582992d-ddd8-4a9d-be4b-60b39e487b62';
        $credentials['server'] = 'https://example.com/oauth/token';

        $expected['credentials']['default'] = $credentials;
        $expected['credentials']['domains'] = [];

        $sources[] = __DIR__ . '/fixtures/default_credentials.yaml';

        $this->assertProcessedConfigurationEquals($expected, $sources);
    }

    /**
     * @test
     */
    public function load_domain_credentials()
    {
        $credentials['client'] = 'test';
        $credentials['secret'] = '8582992d-ddd8-4a9d-be4b-60b39e487b62';
        $credentials['server'] = 'https://example.com/oauth/token';
        $credentials['hosts'][0] = 'example.org';

        $expected['credentials']['domains'][0] = $credentials;

        $sources[] = __DIR__ . '/fixtures/domain_credentials.yaml';

        $this->assertProcessedConfigurationEquals($expected, $sources);
    }

    /**
     * @test
     */
    public function load_xml_config()
    {
        $credentials['client'] = 'test';
        $credentials['secret'] = '8582992d-ddd8-4a9d-be4b-60b39e487b62';
        $credentials['server'] = 'https://example.com/oauth/token';

        $expected['credentials']['default'] = $credentials;
        $expected['credentials']['domains'][0] = $credentials;
        $expected['credentials']['domains'][0]['hosts'][0] = 'example.org';

        $sources[] = __DIR__ . '/fixtures/config.xml';

        $this->assertProcessedConfigurationEquals($expected, $sources);
    }
}
