<?php

namespace SLLH\HybridAuthBundle\Security\Http\EntryPoint;

use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Http\HttpUtils,
    Symfony\Component\HttpFoundation\Request;

/**
 * HybridAuthEntryPoint
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class HybridAuthEntryPoint implements AuthenticationEntryPointInterface
{
    /**
     * @var Symfony\Component\Security\Http\HttpUtils
     */
    private $httpUtils;
    
    /**
     * @var string 
     */
    private $loginPath;
    
    /**
     * Constructor
     * 
     * @param HttpUtils             $httpUtils
     * @param string                $loginPath
     */
    public function __construct(HttpUtils $httpUtils, $loginPath)
    {
        $this->httpUtils = $httpUtils;
        $this->loginPath = $loginPath;
    }

    /**
     * @{inheritDoc} 
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return $this->httpUtils->createRedirectResponse($request, $this->loginPath);
    }
}

?>
