<?php

namespace SLLH\HybridAuthBundle\Security\Core\Exception;

/**
 * HybridAuthException
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
interface HybridAuthExceptionInterface
{
    /**
     * Set the access token
     * 
     * @param string $accessToken 
     */
    public function setAccessToken($accessToken);
    
    /**
     * Get the access token
     * 
     * @return string 
     */
    public function getAccessToken();
    
    /**
     * Set the HybridAuth provider name
     * 
     * @param string $providerName 
     */
    public function setProviderName($providerName);

    /**
     * Get the HybridAuth provider name
     * 
     * @return string
     */
    public function getProviderName();
}

?>
