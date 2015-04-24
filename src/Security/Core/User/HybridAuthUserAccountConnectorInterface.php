<?php

namespace SLLH\HybridAuthBundle\Security\Core\User;

use SLLH\HybridAuthBundle\HybridAuth\HybridAuthResponseInterface;

/**
 * HybridAuthUserAccountConnectorInterface
 * Link a social network account to the user object
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
interface HybridAuthUserAccountConnectorInterface
{
    /**
     * Connect user to social network account
     * 
     * @param mixed $user                               User object
     * @param HybridAuthResponseInterface $response     Account informations
     */
    public function connect($user, HybridAuthResponseInterface $response);
}

?>
