<?php

namespace SLLH\HybridAuthBundle\Security\Core\Authentication\Provider;

use SLLH\HybridAuthBundle\Security\Core\Authentication\Token\HybridAuthToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class HybridAuthProvider implements AuthenticationProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        throw new AuthenticationException('HybridAuth authentication not implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof HybridAuthToken;
    }
}
