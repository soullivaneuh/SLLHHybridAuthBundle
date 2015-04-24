<?php

namespace SLLH\HybridAuthBundle\HybridAuth\Response;

/**
 * AbstractHybridAuthResponse
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class TwitterAuthResponse extends HybridAuthResponse
{
    /**
     * {@inheritDoc}
     */
    public function getFirstName()
    {
        $tab = explode(' ', $this->getUserProfile()->firstName);
        if (isset($tab[0])) {
            return $tab[0];
        }
        return NULL;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastName()
    {
        $tab = explode(' ', $this->getUserProfile()->firstName);
        if (count($tab) > 1) {
            unset($tab[0]);
            $lastName = implode(' ', $tab);
            return $lastName;
        }
        return NULL;
    }
}

?>
