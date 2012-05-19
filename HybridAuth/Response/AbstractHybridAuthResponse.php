<?php

namespace SLLH\HybridAuthBundle\HybridAuth\Response;

use SLLH\HybridAuthBundle\HybridAuth\HybridAuthResponseInterface;

use \Hybrid_Provider_Adapter;

use \DateTime;

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
     * @var Hybrid_User_Profile
     */
    protected $userProfile;
    
    /**
     * Constructor
     * 
     * @param Hybrid_Provider_Adapter $adapter 
     */
    public function __construct(Hybrid_Provider_Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->userProfile = $this->adapter->getUserProfile();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getIdentifier()
    {
        return $this->getUserProfile()->identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername()
    {
        return $this->getUserProfile()->displayName;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getEmail()
    {
        return $this->getUserProfile()->email;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getFirstName()
    {
        return $this->getUserProfile()->firstName;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getLastName()
    {
        return $this->getUserProfile()->lastName;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getBirthDay()
    {
        $day = $this->getUserProfile()->birthDay;
        $month = $this->getUserProfile()->birthMonth;
        $year = $this->getUserProfile()->birthYear;
        if (!empty($day) && !empty($month) && !empty($year)) {
            return new DateTime($year.'-'.$month.'-'.$day);
        }
        return null;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getUserProfile()
    {
        return $this->userProfile;
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
    public function getProviderName()
    {
        $this->adapter->id;
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
