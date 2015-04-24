<?php

namespace SLLH\HybridAuthBundle\Form\Handler;

use Symfony\Component\Form\Form,
    Symfony\Component\HttpFoundation\Request;

use SLLH\HybridAuthBundle\HybridAuth\Response\HybridAuthResponse;

/**
 * RegistrationFormHandlerInterface
 * 
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
interface RegistrationFormHandlerInterface
{
    /**
     * Process and validate the form
     * 
     * @param Request $request
     * @param Form $form
     * @param HybridAuthResponse $response      Contain user informations
     * 
     * @return boolean                          True if form validated
     */
    public function process(Request $request, Form $form, HybridAuthResponse $response);
}

?>
