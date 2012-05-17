<?php

namespace SLLH\HybridAuthBundle\Security\Http\Firewall;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener,
    Symfony\Component\HttpFoundation\Request;

use SLLH\HybridAuthBundle\Security\Http\HybridAuthProviderMap,
    SLLH\HybridAuthBundle\Security\Core\Authentication\Token\HybridAuthToken;

use \Hybrid_Auth;
use \Hybrid_Provider_Adapter;

/**
 * Description of HybridAuthListener
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class HybridAuthListener extends AbstractAuthenticationListener
{
    /**
     * @var HybridAuthProviderMap
     */
    private $providerMap;
    
    /**
     * @var array
     */
    private $checkPaths;
    
    /**
     * Set providerMap, called from HybridAuthFactory
     * 
     * @param HybridAuthProviderMap $providerMap
     */
    public function setProviderMap(HybridAuthProviderMap $providerMap)
    {
        $this->providerMap = $providerMap;
    }
    
    /**
     * Set checkPaths, called from HybridAuthFactory
     * 
     * @param array $checkPaths 
     */
    public function setCheckPaths(array $checkPaths)
    {
        $this->checkPaths = $checkPaths;
    }
    
    /**
     * {@inheritDoc}
     */
    public function requiresAuthentication(Request $request)
    {
        foreach ($this->checkPaths as $checkPath) {
            if ($this->httpUtils->checkRequestPath($request, $checkPath)) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        // Get a Hybrid_Provider_Adapter by user's authentication to the social network with HybridAuth
        $adapter = $this->providerMap->getProviderAdapterByRequest($request);
        
        // Create a token with the social network authentication
        $adapterToken = $adapter->getAccessToken();
        $token =  new HybridAuthToken($adapterToken['access_token'], $adapter->id);
        
        return $this->authenticationManager->authenticate($token);
    }
}

?>
