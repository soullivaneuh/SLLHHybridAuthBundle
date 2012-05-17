<?php

namespace SLLH\HybridAuthBundle\Security\Core\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Core\User\UserProviderInterface;

use SLLH\HybridAuthBundle\Security\Core\Authentication\Token\HybridAuthToken,
    SLLH\HybridAuthBundle\Security\Http\HybridAuthProviderMap,
    SLLH\HybridAuthBundle\Security\Core\User\HybridAuthAwareUserProviderInterface;

/**
 * Description of HybridAuthProvider
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class HybridAuthProvider implements AuthenticationProviderInterface
{
    /**
     * @var HybridAuthAwareUserProviderInterface
     */
    private $userProvider;

    /**
     * @var HybridAuthProviderMap
     */
    private $providerMap;

    /**
     * Constructor
     * 
     * @param HybridAuthAwareUserProviderInterface $userProvider
     * @param HybridAuthProviderMap $providerMap 
     */
    public function __construct(HybridAuthAwareUserProviderInterface $userProvider, HybridAuthProviderMap $providerMap)
    {
        $this->userProvider = $userProvider;
        $this->providerMap = $providerMap;
    }
    
    /**
     * {@inheritDoc}
     */
    public function authenticate(TokenInterface $token)
    {
        die('authenticate');
        // TODO: auth...
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
