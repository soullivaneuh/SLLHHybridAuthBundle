<?php

namespace SLLH\HybridAuthBundle\Form\Handler;

use SLLH\HybridAuthBundle\HybridAuth\Response\HybridAuthResponse;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

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
