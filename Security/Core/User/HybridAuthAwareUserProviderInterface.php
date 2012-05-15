<?php

namespace SLLH\HybridAuthBundle\Security\Core\User;

use SLLH\HybridAuthBundle\HybridAuth\HybridAuthResponseInterface;

/**
 * HybridAuthAwareUserProviderInterface
 * 
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
interface HybridAuthAwareUserProviderInterface
{
    /**
     * Loads the user by a given HybridAuthResponseInterface object.
     *
     * @param HybridAuthResponseInterface $response
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    function loadUserByHybridAuthResponse(HybridAuthResponseInterface $response);    
}

?>
