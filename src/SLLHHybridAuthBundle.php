<?php

namespace SLLH\HybridAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\HttpKernel\Kernel;

use SLLH\HybridAuthBundle\DependencyInjection\SLLHHybridAuthExtension,
    SLLH\HybridAuthBundle\DependencyInjection\Security\Factory\HybridAuthFactory;

class SLLHHybridAuthBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        //  For 2.0 versions :
        //     factories:
        //             - "%kernel.root_dir%/../../../vendor/bundles/SLLH/HybridAuthBundle/Resources/config/security_factory.xml"
        if (version_compare(Kernel::VERSION, '2.1-DEV', '>=')) {
            $extension = $container->getExtension('security');
            $extension->addSecurityListenerFactory(new HybridAuthFactory());
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        // for get sllh_hybridauth instead of sllh_hybrid_auth
        if (null === $this->extension) {
            return new SLLHHybridAuthExtension;
        }

        return $this->extension;
    }
}
