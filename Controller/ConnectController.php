<?php

namespace SLLH\HybridAuthBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Core\SecurityContext,
    Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Locale\Exception\NotImplementedException,
    Symfony\Component\Form\Form;
    
use SLLH\HybridAuthBundle\Security\Core\Exception\AccountNotLinkedException,
    SLLH\HybridAuthBundle\HybridAuth\Response\HybridAuthResponse;

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
    public final function connectAction(Request $request)
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

        // TODO: make a success_path in security.yml or config.yml
        return new RedirectResponse($this->container->get('router')->generate('homepage'));
        
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
    public final function registerAction(Request $request)
    {
        $connect = $this->container->getParameter('sllh_hybridauth.connect');
        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        $session = $request->getSession();
        
        // Get and remove error from session
        $error = $session->get('hybrid_auth.connection_error');
        
        
        // Check if connect option is enabled
        if (!$connect) {
            throw new \Exception("Connect option MUST be activated");
        }
        // Redirect to homepage if there are nothing to do there :)
        if ($hasUser || !$error instanceof AccountNotLinkedException) {
            return new RedirectResponse($this->container->get('router')->generate('homepage'));
        }
        
        // Get social account informations
        $adapter = $this->container->get('sllh_hybridauth.provider_map')->getProviderAdapterByName($error->getProviderName());
        $response = new HybridAuthResponse($adapter); // TODO: check return value
        
        // Get form and form handler form config.yml
        $form = $this->container->get('sllh_hybridauth.registration.form');
        $formHandler = $this->container->get('sllh_hybridauth.registration.form.handler'); // TODO: check if the class implements good interface
        $processed = $formHandler->process($request, $form, $response); // TODO: make an interface to implement
        if ($processed) {
            // Removing session cause of succed
            $session->remove('hybrid_auth.connection_error');
            
            // Now we link the account to the created user
            // TODO: check if connect_provider implement good classes
            $this->container->get('sllh_hybridauth.connect.provider')->connect($form->getData(), $response);
            
            // TODO: athenticate user ? mail-confirmation FOS ?
            // TODO: add param for register_success path ? twig_template ?
            return $this->registerActionSuccess($request, $form, $response);
        }
        
        
        return $this->registerActionSuccess($request, $form, $response, $error);
    }
    
    protected function registerActionSuccess(Request $request, Form $form, HybridAuthResponse $response, AccountNotLinkedException $error = NULL)
    {
        // Register and connect done
        if ($error === NULL) {
            return new RedirectResponse($this->container->get('router')->generate('homepage'));
        }
        // TODO: Make multiple template engines compatibility (see: FOSUserBundle/Controllers)
        return $this->container->get('templating')->renderResponse('SLLHHybridAuthBundle:Connect:register.html.twig', array(
            'error'             => $error->getMessage(),
            'form'              => $form->createView(),
            'provider'          => $response->getProviderName(),
            'user_profile'      => $response->getUserProfile()
        ));
    }
    
    /**
     * Link a social network account to a 
     * 
     * @param Request $request 
     * @param string $name          Name of the social network
     * 
     * @return Response
     */
    public final function linkAction(Request $request, $name)
    {
        $connect = $this->container->getParameter('sllh_hybridauth.connect');
        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        $session = $request->getSession();
        
        // Redirect to homepage if connect option is not set or if there is no authenticated user
        if (!$connect && !$hasUser) {
            return new RedirectResponse($this->container->get('router')->generate('homepage'));
        }
        
        // Get social account informations
        $adapter = $this->container->get('sllh_hybridauth.provider_map')->getProviderAdapterByName($name);
        $response = new HybridAuthResponse($adapter); // TODO: check return value
        
        // Get identifier from cofirmation
        $uid = $session->get('hybrid_auth.link_confirm');
        $session->remove('hybrid_auth.link_confirm');
        
        $form = $this->container->get('form.factory')->createBuilder('form')->getForm();
        
        // Check if the social identifier is the same after confirmation
        if ($request->getMethod() === 'POST' && $uid === $response->getIdentifier()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $user = $this->container->get('security.context')->getToken()->getUser();
                
                // Links the user to the new social network
                // TODO: check if the class connector implements good interface
                $this->container->get('sllh_hybridauth.connect.provider')->connect($user, $response);
                
                return $this->container->get('templating')->renderResponse('SLLHHybridAuthBundle:Connect:link_success.html.twig', array(
                    'provider'      => $adapter->id,
                    'user_profile'  => $response->getUserProfile()
                ));
            }
        }
        
        // Set identifier for confirmation
        $session->set('hybrid_auth.link_confirm', $response->getIdentifier());
        
        return $this->container->get('templating')->renderResponse('SLLHHybridAuthBundle:Connect:link_confirmation.html.twig', array(
            'form'          => $form->createView(),
            'provider'      => $adapter->id,
            'user_profile'  => $response->getUserProfile()
        ));
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
