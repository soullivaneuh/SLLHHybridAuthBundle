<?php

namespace SLLH\HybridAuthBundle\HybridAuth\Response;

use SLLH\HybridAuthBundle\HybridAuth\HybridAuthResponseInterface;

use \Hybrid_Provider_Adapter;

use \DateTime;
use \Normalizer;

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
    
    /**        return normalizer_normalize($str);
        

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
    public final function getIdentifier()
    {
        return $this->getUserProfile()->identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername()
    {
        return $this->cleanString($this->getUserProfile()->displayName);
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
    public function getBirthday()
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
    public final function getUserProfile()
    {
        return $this->userProfile;
    }

    /**
     * {@inheritDoc}
     */
    public final function getUserContactsList()
    {
        // TODO: try...catch...return null
        return $this->adapter->getUserContactsList();
    }

    /**
     * {@inheritDoc}
     */
    public final function getUserActivity()
    {
        // TODO: try...catch...return null
        return $this->adapter->getUserActivity();
    }

    /**
     * {@inheritDoc}
     */
    public final function getProviderName()
    {
        return $this->adapter->id;
    }

    /**
     * {@inheritDoc}
     */
    public final function getProviderAdapter()
    {
        return $this->adapter;
    }
    
    /**
     * Remove all specials characters
     * 
     * @param string $str
     * 
     * @return string 
     */
    private final function cleanString($str)
    {
        $search = array('@[^a-zA-Z0-9_]@');
        $replace = array('');
        return preg_replace($search, $replace, $str);
    }
}

?>
