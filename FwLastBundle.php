<?php

namespace Fw\LastBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Fw\LastBundle\DependencyInjection\Compiler\RouteProviderPass;

class FwLastBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RouteProviderPass());
    }
}
