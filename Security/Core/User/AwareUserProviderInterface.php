<?php

namespace SLLH\HybridAuthBundle\Security\Core\User;

/**
 * HybridAuthAwareUserProviderInterface
 * 
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
interface HybridAuthAwareUserProviderInterface
{
    /**
     * Loads the user by a given UserResponseInterface object.
     *
     * @param UserResponseInterface $response
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    function loadUserByHybridAuthResponse(HybridAuthResponseInterface $response);    
}

?>
