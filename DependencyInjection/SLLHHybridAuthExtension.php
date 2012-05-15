<?php

namespace SLLH\HybridAuthBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator,
    Symfony\Component\Config\Definition\Processor,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Reference,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
    Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\DependencyInjection\DefinitionDecorator;

/**
 * Description of SLLHHybridAuthExtension
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class SLLHHybridAuthExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
    }
    
    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'sllh_hybridauth';
    }
}

?>