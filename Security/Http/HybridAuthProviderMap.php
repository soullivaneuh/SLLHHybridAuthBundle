<?php

namespace SLLH\HybridAuthBundle\Security\Http;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Http\HttpUtils,
    Symfony\Component\DependencyInjection\ContainerInterface;

use \Hybrid_Auth;

/**
 * Description of HybridAuthProviderMap
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class HybridAuthProviderMap
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * @var HttpUtils
     */
    private $httpUtils;
    
    /**
     * @var array 
     */
    private $providers;
    
    /**
     * @var Hybrid_Auth 
     */
    private $hybridauth;
    
    /**
     * Constructor
     * 
     * @param HttpUtils $httpUtils          HttpUtils
     * @param array     $config             HybridAuth config
     * @param array     $providers          Configured providers with checkPaths in security configuration file
     */
    public function __construct(ContainerInterface $container, HttpUtils $httpUtils, array $providers)
    {
        $this->container = $container;
        $this->httpUtils = $httpUtils;
        $this->providers = $providers;
        $this->hybridauth = false;
    }
    
    /**
     * Gets the appropriate ProviderAdapter given the name.
     * 
     * @param string $name 
     * 
     * @return null|Hybrid_Provider_Adapter
     */
    public function getProviderAdapterByName($name)
    {
        $hybridauth_config = $this->container->getParameter('sllh_hybridauth.config');
        if (!array_key_exists($name, $hybridauth_config['providers'])) {
            return null;
        } // TODO: Throw directly if provider not configured ?
        
        // TODO: Catch error and return null: Authentification failed! Facebook returned an invalide user id.
        // TODO: add additional params ($this->config['providers'][$name]['auth_params'])
        return $this->getHybridAuth()->authenticate($name);
    }
    
    /**
     * Gets the appropriate ProviderAdapter for a request
     * 
     * @param type $request
     * 
     * @return null|Hybrid_Provider_Adapter
     */
    public function getProviderAdapterByRequest(Request $request)
    {
        foreach ($this->providers as $name => $checkPath) {
            if ($this->httpUtils->checkRequestPath($request, $checkPath)) {
                return $this->getProviderAdapterByName($name);
            }
        }
        return null;
    }
        
    /**
     * Gets the Hybrid_Auth api
     * 
     * @return type 
     */
    public function getHybridAuth()
    {
        if ($this->hybridauth === false) {
            $this->hybridauth = new Hybrid_Auth($this->container->getParameter('sllh_hybridauth.config'));
        }
        return $this->hybridauth;
    }
}

?>
