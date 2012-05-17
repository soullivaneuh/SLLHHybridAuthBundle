<?php

namespace SLLH\HybridAuthBundle\Security\Core\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Description of HybridAuthToken
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class HybridAuthToken extends AbstractToken
{    
    /**
     * @var string 
     */
    private $accessToken;
    
    /**
     * @var string 
     */
    private $provider;
    
    /**
     * @param string $accessToken   Social Network access token
     * @param string $provider      HybridAuth provider name
     * @param array  $roles         Roles for the token
     */
    public function __construct($accessToken, $provider, array $roles = array())
    {
        $this->accessToken = $accessToken;
        $this->provider = $provider;
        parent::__construct($roles);
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentials()
    {
        return $this->accessToken;
    }
    
    /**
     * Gets the name of the provider
     * 
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }
}

?>
