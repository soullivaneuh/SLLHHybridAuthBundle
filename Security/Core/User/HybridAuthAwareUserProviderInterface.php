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
    
    
    /**
     * Loads the user by a given social name and identifier
     *
     * @param string $name          Name of the social network
     * @param string $identifier
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    function loadUserByIdentifier($name, $identifier);
}

?>
