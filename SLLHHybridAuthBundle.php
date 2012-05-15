<?php

namespace SLLH\HybridAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use SLLH\HybridAuthBundle\DependencyInjection\SLLHHybridAuthExtension;

class SLLHHybridAuthBundle extends Bundle
{
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
