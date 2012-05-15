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
     * Get user social network identifier 
     * 
     * @return string
     */
    public function getIdentifier();
    
    /**
     * Get user social network profile
     * 
     * @return Hybrid_User_Profile
     */
    public function getUserProfile();
    
    /**
     * Get user social network contacts list 
     * 
     * @return array
     */
    public function getUserContactsList();
    
    /**
     * Get user social network activity
     * 
     * @return array 
     */
    public function getUserActivity();
    
    /**
     * Get the Hybrid_Provider_Adapter of the authenticated user
     * 
     * @return Hybrid_Provider_Adapter 
     */
    public function getProviderAdapter();
}

?>
