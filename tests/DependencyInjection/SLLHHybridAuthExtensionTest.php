<?php

namespace SLLH\HybridAuthBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use SLLH\HybridAuthBundle\DependencyInjection\SLLHHybridAuthExtension;

class SLLHHybridAuthExtensionTest extends AbstractExtensionTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return array(
            new SLLHHybridAuthExtension(),
        );
    }

    public function testLoad()
    {
        $this->load();
    }
}
