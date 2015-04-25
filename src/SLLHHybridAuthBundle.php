<?php

namespace SLLH\HybridAuthBundle;

use SLLH\HybridAuthBundle\DependencyInjection\Security\Factory\HybridAuthFactory;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class SLLHHybridAuthBundle
 *
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
class SLLHHybridAuthBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var SecurityExtension $extension */
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new HybridAuthFactory());
    }
}
