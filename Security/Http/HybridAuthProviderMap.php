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
        $hybridauth_config = $this->container('sllh_hybridauth.config');
        if (!array_key_exists($name, $hybridauth_config['providers'])) {
            return null;
        }
        
        $hybridauth = new Hybrid_Auth($hybridauth_config);
        
        return $hybridauth->authenticate($name); // TODO: add additional params ($this->config['providers'][$name]['auth_params'])
    }
    
    /**
     * Gets the appropriate ProviderAdapter for a request
     * 
     * @param type $request
     * 
     * @return null|array
     */
    public function getProviderAdapterByRequest(Request $request)
    {
        foreach ($this->providers as $name => $checkPath) {
            if ($this->httpUtils->checkRequestPath($request, $checkPath)) {
                return array($this->getProviderAdapterByName($name), $checkPath);
            }
        }
        return null;
    }
}

?>
