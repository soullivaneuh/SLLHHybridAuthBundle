<?php

namespace SLLH\HybridAuthBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class SLLHHybridAuthExtension
 *
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
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
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'sllh_hybrid_auth';
    }
}
