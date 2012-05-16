<?php

namespace SLLH\HybridAuthBundle\Security\Http\Firewall;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener,
    Symfony\Component\HttpFoundation\Request;

use SLLH\HybridAuthBundle\Security\Http\HybridAuthProviderMap;

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
        // TODO: check path by enabled owners
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        // TODO: get hybridauth authentificationManager

        return $this->authenticationManager->authenticate($token);
    }
}

?>
