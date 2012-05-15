<?php

namespace SLLH\HybridAuthBundle\Security\Core\Authentification\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Core\User\UserProviderInterface;

use SLLH\HybridAuthBundle\Security\Core\Authentification\Token\HybridAuthToken,
    SLLH\HybridAuthBundle\Security\Http\HybridAuthProviderMap;

/**
 * Description of HybridAuthProvider
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class HybridAuthProvider implements AuthenticationProviderInterface
{
    /**
     * @var HybridAuthProviderMap
     */
    private $providerMap;
    
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    public function __construct()
    {
        
    }
    
    /**
     * {@inheritDoc}
     */
    public function authenticate(TokenInterface $token)
    {
        
    }
    
    /**
     * {@inheritDoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof HybridAuthToken;
    }
}

?>
