<?php

namespace SLLH\HybridAuthBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class HybridAuthFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $providerId = 'sllh_hybrid_auth.security.authentication.provider.'.$id;

        $container
            ->setDefinition($providerId, new DefinitionDecorator('sllh_hybrid_auth.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProviderId))
        ;

        return $providerId;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListenerId()
    {
        return 'sllh_hybrid_auth.security.authentication.listener';
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return 'http';
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'hybrid_auth';
    }
}
