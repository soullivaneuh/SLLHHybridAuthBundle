<?php

namespace SLLH\HybridAuthBundle\Security\Core\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * AccountNotLinkedException
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class AccountNotConnectedException extends AuthenticationException
    implements HybridAuthExceptionInterface
{
    /**
     * @var string
     */
    private $accessToken;
    
    /**
     * @var string
     */
    private $providerName;
    
    
    /**
     * {@inheritDoc}
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * {@inheritDoc}
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setProviderName($providerName)
    {
        $this->providerName = $providerName;
    }

    /**
     * {@inheritDoc}
     */
    public function getProviderName()
    {
        return $this->providerName;
    }
    
    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->accessToken,
            $this->providerName,
            parent::serialize()
        ));
    }
    
    /**
     * {@inheritDoc} 
     */
    public function unserialize($str)
    {
        list(
            $this->accessToken,
            $this->providerName,
            $parentData
        ) = unserialize($str);
        parent::unserialize($parentData);
    }
}

?>
