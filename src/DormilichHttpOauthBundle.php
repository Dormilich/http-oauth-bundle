<?php

namespace Dormilich\Bundle\HttpOauthBundle;

use Dormilich\Bundle\HttpOauthBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DormilichHttpOauthBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ProviderCompilerPass());
    }
}
