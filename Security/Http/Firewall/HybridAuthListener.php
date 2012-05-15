<?php

namespace SLLH\HybridAuthBundle\Security\Http\Firewall;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener,
    Symfony\Component\HttpFoundation\Request;

/**
 * Description of HybridAuthListener
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class HybridAuthListener extends AbstractAuthentificationListener
{
    /**
     * @var array
     */
    private $checkPaths;
    
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
