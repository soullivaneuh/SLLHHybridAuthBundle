<?php

namespace SLLH\HybridAuthBundle\Security\Core\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Core\User\UserProviderInterface,
    Symfony\Component\Security\Core\User\UserCheckerInterface;

use SLLH\HybridAuthBundle\Security\Core\Authentication\Token\HybridAuthToken,
    SLLH\HybridAuthBundle\Security\Http\HybridAuthProviderMap,
    SLLH\HybridAuthBundle\Security\Core\User\HybridAuthAwareUserProviderInterface,
    SLLH\HybridAuthBundle\Security\Core\Exception\HybridAuthExceptionInterface;

use SLLH\HybridAuthBundle\HybridAuth\Response\HybridAuthResponse,
    SLLH\HybridAuthBundle\HybridAuth\Response\TwitterAuthResponse;

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
     * @var UserCheckerInterface
     */
    private $userChecker;

    /**
     * Constructor
     * 
     * @param HybridAuthAwareUserProviderInterface $userProvider
     * @param HybridAuthProviderMap $providerMap 
     */
    public function __construct(HybridAuthAwareUserProviderInterface $userProvider, HybridAuthProviderMap $providerMap, UserCheckerInterface $userChecker)
    {
        $this->userProvider = $userProvider;
        $this->providerMap = $providerMap;
        $this->userChecker = $userChecker;
    }
    
    /**
     * {@inheritDoc}
     */
    public function authenticate(TokenInterface $token)
    {
        $adapter = $this->providerMap->getProviderAdapterByName($token->getProvider());
        
        // Making a HybridAuthResponse
        $responseNamespace = 'SLLH\HybridAuthBundle\HybridAuth\Response\\';
        $responseClass = $responseNamespace.$adapter->id.'AuthResponse';
        if (class_exists($responseClass)) {
            $response = new $responseClass($adapter);
        }
        else {
            $response = new HybridAuthResponse($adapter);            
        }
        
        // Getting the user by the selected provider
        try {
            // TODO: check if userProvider implements good classes
            $user = $this->userProvider->loadUserByHybridAuthResponse($response);
            
            // Advanced account status check
            $this->userChecker->checkPostAuth($user);
        }
        catch (HybridAuthExceptionInterface $e) { // Follow information to ConnectController
            $e->setAccessToken($token->getCredentials());
            $e->setProviderName($token->getProvider());
            throw $e;
        }
        
        // Creating new token to athenticate use
        $token = new HybridAuthToken($token->getCredentials(), $token->getProvider(), $user->getRoles());
        $token->setUser($user);
        $token->setAuthenticated(true);
        
        return $token;
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
