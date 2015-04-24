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
    // TODO: Add a connect api service like '/social/{name}/api/me/contact_lists'
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('hybridauth.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('sllh_hybridauth.firewall_name', $config['firewall_name']);

        // Clean and set HybridAuth config
        foreach ($config['hybridauth_config']['providers'] as $name => $provider) {
            if (isset($provider['scope']) && empty($provider['scope'])) { // TODO: find an other way with the three builder
                unset($config['hybridauth_config']['providers'][$name]['scope']);
            }
        }
        $config['hybridauth_config']['debug_mode'] = $config['hybridauth_config']['debug_file'] != '' ? true : false;
        $container->setParameter('sllh_hybridauth.config', $config['hybridauth_config']);

//        $providers_names = array();
//        foreach ($config['hybridauth_config'] as $name => $p) {
//            $providers_names[] = $name;
//        }
//        $container->setParameter('sllh_hybridauth.providers', $providers_names

        $container->setParameter('sllh_hybridauth.connect', isset($config['connect']));
        if (isset($config['connect'])) {
            // Links the specified service for connect
            foreach ($config['connect']['services'] as $key => $serviceId) {
                $container->setAlias('sllh_hybridauth.'.str_replace('_', '.', $key), $serviceId);
            }

            $container->setParameter('sllh_hybridauth.auth_after_register', $config['connect']['auth_after_register']);

            $container->setAlias('sllh_hybridauth.user_checker', 'security.user_checker');
        }
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
