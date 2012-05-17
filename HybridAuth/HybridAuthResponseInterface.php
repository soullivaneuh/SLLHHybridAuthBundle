<?php

namespace SLLH\HybridAuthBundle\HybridAuth;

use \Hybrid_User_Profile,
    \Hybrid_Provider_Adapter;

/**
 * HybridAuthResponseInterface
 * 
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
interface HybridAuthResponseInterface
{
    /**
     * Gets user social network identifier 
     * 
     * @return string
     */
    public function getIdentifier();
    
    /**
     * Gets as possibly a correct username
     * 
     * @return null|string 
     */
    public function getUsername();
    
    /**
     * Gets as possibly a correct email
     * 
     * @return null|string 
     */
    public function getEmail();
    
    /**
     * Gets as possibly a correct first name
     * 
     * @return null|string 
     */
    public function getFirstName();
    
    /**
     * Gets as possibly a correct last name
     * 
     * @return null|string 
     */
    public function getLastName();
    
    /**
     * Gets as possibly a correct birthday object
     * 
     * @return null|DateTime
     */
    public function getBirthDay();
    
    /**
     * Gets user social network profile
     * 
     * @return Hybrid_User_Profile
     */
    public function getUserProfile();
    
    /**
     * Getss user social network contacts list 
     * 
     * @return null|array
     */
    public function getUserContactsList();
    
    /**
     * Gets user social network activity
     * 
     * @return null|array 
     */
    public function getUserActivity();
    
    /**
     * Gets the Hybrid_Provider_Adapter of the authenticated user
     * 
     * @return Hybrid_Provider_Adapter 
     */
    public function getProviderAdapter();
}

?>
