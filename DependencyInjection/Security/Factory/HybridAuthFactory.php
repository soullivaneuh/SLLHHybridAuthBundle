<?php

namespace SLLH\HybridAuthBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory,
    Symfony\Component\Config\Definition\Builder\NodeDefinition,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\DefinitionDecorator,
    Symfony\Component\DependencyInjection\Parameter,
    Symfony\Component\DependencyInjection\Reference;

/**
 * Description of HybridAuthFactory
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class HybridAuthFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     */
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $providerId = 'sllh_hybridauth.authentication.provider.hybridauth.'.$id;
        
        $container
            ->setDefinition($providerId, new DefinitionDecorator('sllh_hybridauth.authentication.provider.hybridauth'))
            ->addArgument($this->createHybridAuthAwareUserProvider($container, $id, $config['user_provider']));

        return $providerId;
    }

    /**
     * Assign the selected userProvider
     * 
     * @param ContainerBuilder $container
     * @param string $id
     * @param array $config
     * 
     * @return \Symfony\Component\DependencyInjection\Reference 
     */
    protected function createHybridAuthAwareUserProvider(ContainerBuilder $container, $id, $config)
    {
        $serviceId = 'sllh_hybridauth.user.provider.entity.'.$id;

        switch(key($config)) { // TODO: add a default provider
//            case 'hybridauth':
//                $container
//                    ->setDefinition($serviceId, new DefinitionDecorator('sllh_hybridauth.user.provider'));
//                break;
            case 'service':
                $container
                    ->setAlias($serviceId, $config['service']);
                break;
        }

        return new Reference($serviceId);
    }

    /**
     * {@inheritDoc}
     */
//    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
//    {
//        $entryPointId = 'sllh_hybridauth.authentication.entry_point.hybridauth.'.$id;
//
//        $entryPointDefinition = $container
//            ->setDefinition($entryPointId, new DefinitionDecorator('sllh_hybridauth.authentication.entry_point.hybridauth'))
//            ->addArgument(new Reference('security.http_utils'))
//            ->addArgument($config['login_path']);
//
//        return $entryPointId;
//    }
    
    /**
     * {@inheritDoc}
     */
    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = parent::createListener($container, $id, $config, $userProvider);

        $checkPaths = array(); // TODO: recuperer les noms des providers !
        foreach ($container->getParameter('sllh_hybridauth.providers') as $provider) {
            $checkPaths[] = $config['check_prefix_path'].'/'.strtolower($provider);
            // TOTO: check if user set '/' in the end of the prefix
        }

        $container->getDefinition($listenerId)
            ->addMethodCall('setCheckPaths', array($checkPaths));

        return $listenerId;
    }
    
    /**
     * {@inheritDoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);
        
        $builder = $node->children();
        
        $builder
            ->scalarNode('login_path')
                ->cannotBeEmpty()
                ->isRequired()
            ->end()
            ->scalarNode('check_prefix_path')
                ->cannotBeEmpty()
                ->isRequired()
            ->end()
            ->arrayNode('user_provider') // TODO: add more providers (orm, fos...)
                ->isRequired()
                ->children()
                    ->scalarNode('service')
                        ->cannotBeEmpty()
                    ->end()
                ->end()
                ->validate()
                    ->ifTrue(function($up) {
                        return 1 !== count($up) || !in_array(key($up), array('service'));
                    })
                    ->thenInvalid("You should configure (only) one of: 'service'.")
                ->end()
            ->end()
        ;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function getListenerId()
    {
        return 'shhl_hybridauth.authentication.listener.hybridauth';
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return 'hybridauth';
    }

    /**
     * {@inheritDoc}
     */
    public function getPosition()
    {
        return 'http';
    }
}

?>
