<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 13.08.18
 * Time: 14:19
 */

namespace Fw\LastBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

class FwLastExtension extends Extension
{

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if(!empty($config['dist_folder'])) {
            $definition = $container->getDefinition('fw_last.site_generator');
            $definition->replaceArgument(3, $config['dist_folder']);
        }

        if(isset($config['providers']['static'])) {
            $definition = $container->getDefinition('Fw\LastBundle\Router\Provider\StaticProvider');
            $definition->replaceArgument(0, (bool)$config['providers']['static']);
        }
    }
}