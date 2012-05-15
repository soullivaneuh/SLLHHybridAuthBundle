<?php

namespace SLLH\HybridAuthBundle\Security\Core\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Description of HybridAuthToken
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class HybridAuthToken extends AbstractToken
{
    /**
     * @var string 
     */
    private $uid;
    
    /**
     * @param string $uid         User social network id
     * @param array  $roles       Roles for the token
     */
    public function __construct($uid, array $roles = array())
    {
        $this->uid = $uid;
        parent::__construct($roles);
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentials()
    {
        return $this->uid;
    }
}

?>
