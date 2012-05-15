<?php

namespace SLLH\HybridAuthBundle\HybridAuth\Response;

use SLLH\HybridAuthBundle\HybridAuth\HybridAuthResponseInterface;

use \Hybrid_Provider_Adapter;

/**
 * AbstractHybridAuthResponse
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class AbstractHybridAuthResponse implements HybridAuthResponseInterface
{
    /**
     * @var Hybrid_Provider_Adapter 
     */
    protected $adapter;
    
    /**
     * Constructor
     * 
     * @param Hybrid_Provider_Adapter $adapter 
     */
    public function __construct(Hybrid_Provider_Adapter $adapter)
    {
        $this->adapter = $adapter;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getIdentifier()
    {
        return $this->adapter->getUserProfile()->identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserProfile()
    {
        return $this->adapter->getUserProfile();
    }

    /**
     * {@inheritDoc}
     */
    public function getUserContactsList()
    {
        // TODO: try...catch...return null
        return $this->adapter->getUserContactsList();
    }

    /**
     * {@inheritDoc}
     */
    public function getUserActivity()
    {
        // TODO: try...catch...return null
        return $this->adapter->getUserActivity();
    }

    /**
     * {@inheritDoc}
     */
    public function getProviderAdapter()
    {
        return $this->adapter;
    }
}

?>
