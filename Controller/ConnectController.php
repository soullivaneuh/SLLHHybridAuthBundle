<?php

namespace SLLH\HybridAuthBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Core\SecurityContext,
    Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Locale\Exception\NotImplementedException,
    Symfony\Component\Form\Form,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    
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
        return $this->container->get('templating')->renderResponse('SLLHHybridAuthBundle:Connect:login.html.twig', array(
            'error'         => $error ? $error->getMessage() : '',
            'providers'     => $this->getProvidersForConnect($request, $hasUser),
        ));
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
        
//        echo '<pre>';
//        print_r($response->getUserProfile());
//        die();
        
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
            
            // Authenticate the user
            if ($this->container->getParameter('sllh_hybridauth.auth_after_register') === true) {
                $this->authenticateUser($form->getData());
            }

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
     * Generate a template with js sdks for providers
     * Auth a provider to hybridauth when the name is given
     * 
     * @param Request $request 
     * @param string $name          Name of the social network
     * 
     * @return Response
     */
    public function authAction(Request $request, $name = null)
    {
        if ($request->cookies->get('sllh_hybridauth_logout')) {
            return new Response();
        }
        
        // TODO: Fix issue when user auth in social network but not in website...

        // TODO: Add option for enabled/disabled auto_connect (TWIG ?)

        // Generate js sdk to check if user connected with your social application
        return $this->container->get('templating')->renderResponse('SLLHHybridAuthBundle:Connect:auth.html.twig', array(
            'providers' => $this->getProvidersForConnect($request, false)
        ));
    }

    public function checkIdentifierAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException("This action can be only call by XHR Post request.");
        }
        $name = $request->request->get('name');
        $identifier = $request->request->get('identifier');
        if (!$name || !$identifier) {
            throw new \RuntimeException("You must pass a name and identifier");
        }
        $userProvider = $this->container->get('sllh_hybridauth.user.provider.entity.'.$this->container->getParameter('sllh_hybridauth.firewall_name'));
        
        try {
            $user = $userProvider->loadUserByIdentifier($name, $identifier);
            return new Response('1', 200, array('Content-Type' => 'text/plain'));
        }
        catch (AuthenticationException $e) {
            return new Response('0', 200, array('Content-Type' => 'text/plain'));
        }
    }

    /**
     * Gets a list of providers for connectAction
     * 
     * @param Request $request
     * @param boolean $hasUser
     * 
     * @return array
     */
    protected function getProvidersForConnect(Request $request, $hasUser)
    {
        $providerMap = $this->container->getParameter('sllh_hybridauth.provider_map.configured.'.$this->container->getParameter('sllh_hybridauth.firewall_name'));
        $configs = $this->container->getParameter('sllh_hybridauth.config');
        
        $providers = array();
        foreach ($providerMap as $name => $checkPath) {
            $providers[$name] = array(
                'url'       => $hasUser
                    ? $this->container->get('router')->generate('hybridauth_connect_link', array('name' => $name))
                    : $request->getUriForPath($checkPath),
                'name'      => $name,
                'config'    => $configs['providers'][$name]
            );
        }
        
        return $providers;
    }
    
    /**
     * Gets a list of providers for authAction
     * 
     * @param Request $request
     * @param boolean $hasUser
     * 
     * @return array
     */
    protected function getProvidersForAuth(Request $request)
    {
        $configs = $this->container->getParameter('sllh_hybridauth.config');
        
        $providers = array();
        foreach ($configs['providers'] as $name => $config) {
            $providers[$name] = array(
                'url'       => $this->container->get('router')->generate('hybridauth_connect_auth', array('name' => $name)),
                'name'      => $name,
                'config'    => $config
            );
        }
        
        return $providers;
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
    
    /**
     * Authenticate a user with Symfony Security
     * 
     * @param UserInterface $user
     */
    protected function authenticateUser(UserInterface $user)
    {
        try {
            $this->container->get('sllh_hybridauth.user_checker')->checkPostAuth($user);
        } catch (AccountStatusException $e) {
            // Don't authenticate locked, disabled or expired users
            return;
        }

        $providerKey = $this->container->getParameter('sllh_hybridauth.firewall_name');
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->container->get('security.context')->setToken($token);
    }    
}

?>
