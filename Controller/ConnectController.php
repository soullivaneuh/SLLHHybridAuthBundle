<?php

namespace SLLH\HybridAuthBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Core\SecurityContext,
    Symfony\Component\Security\Core\User\UserInterface;

use SLLH\HybridAuthBundle\Security\Core\Exception\AccountNotLinkedException;

/**
 * HybridAuthController
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class ConnectController extends ContainerAware
{
    /**
     * Action for login form
     * 
     * If connect enabled, redirect to a registration form if social network account is not linked
     * to the database.
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function connectAction(Request $request)
    {
        $connect = $this->container->getParameter('sllh_hybridauth.connect');
        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        $session = $request->getSession();
        
        $error = $this->getErrorForRequest($request);
        
        // Follow to register form with social network informations
        if ($connect && !$hasUser && $error instanceof AccountNotLinkedException) {
            $key = uniqid($error->getProviderName().'-');
            $session->set('hybrid_auth.connection_error', $error);
            return new RedirectResponse($this->container->get('router')->generate('hybridauth_connect_register'));
        }

        // TODO: Render a twig template with list of provider
        die('Connect:connect');
    }
    
    /**
     * Show a register form with social network account information (fill by form handler)
     * Connect option MUST be enabled for working
     * 
     * @param Request $request 
     * 
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $connect = $this->container->getParameter('sllh_hybridauth.connect');
        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        $session = $request->getSession();
        
        // Get and remove error from session
        $error = $session->get('hybrid_auth.connection_error');
        $session->remove('hybrid_auth.connection_error'); // TODO: remove after process form succeded ?
        
        // Check if connect option is enabled
        if (!$connect) {
            throw new \Exception("Connect option MUST be activated");
        }
        // Redirect to homepage if there are nothing to do there :)
        if ($hasUser || !$error instanceof AccountNotLinkedException) {
            return new RedirectResponse($this->container->get('router')->generate('homepage'));
        }
        
        echo get_class($error).'<br/>';
        die('todo!!!');
    }
    
    /**
     * Gets the security error for a given request.
     *
     * @param Request $request
     *
     * @return null|Exception
     */
    protected function getErrorForRequest(Request $request)
    {
        $session = $request->getSession();
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        else {
            $error = null;
        }

        return $error;
    }
}

?>
