<?php

namespace SLLH\HybridAuthBundle\Security\Http\Firewall;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener,
    Symfony\Component\Security\Http\Firewall\ListenerInterface,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Event\GetResponseEvent,
    Symfony\Component\Security\Core\SecurityContextInterface,
    Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface,
    Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface,
    Symfony\Component\Security\Http\HttpUtils,
    Symfony\Component\HttpKernel\Log\LoggerInterface,
    Symfony\Component\EventDispatcher\EventDispatcherInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Security\Http\Event\InteractiveLoginEvent,
    Symfony\Component\Security\Http\SecurityEvents,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Core\Exception\SessionUnavailableException;

use SLLH\HybridAuthBundle\Security\Http\HybridAuthProviderMap,
    SLLH\HybridAuthBundle\Security\Core\Authentication\Token\HybridAuthToken;

use \Hybrid_Auth;
use \Hybrid_Provider_Adapter;

/**
 * Description of HybridAuthListener
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class HybridAuthListener implements ListenerInterface
{
    protected $options;
    protected $logger;
    protected $authenticationManager;
    protected $providerKey;
    protected $httpUtils;

    private $securityContext;
    private $sessionStrategy;
    private $dispatcher;
    private $successHandler;
    private $failureHandler;
    private $rememberMeServices;
    
    /**
     * @var HybridAuthProviderMap
     */
    private $providerMap;
    
    /**
     * @var array
     */
    private $checkPaths;
    
    /**
     * Constructor.
     *
     * @param SecurityContextInterface               $securityContext       A SecurityContext instance
     * @param AuthenticationManagerInterface         $authenticationManager An AuthenticationManagerInterface instance
     * @param SessionAuthenticationStrategyInterface $sessionStrategy
     * @param HttpUtils                              $httpUtils             An HttpUtilsInterface instance
     * @param string                                 $providerKey
     * @param array                                  $options               An array of options for the processing of a
     *                                                                      successful, or failed authentication attempt
     * @param AuthenticationSuccessHandlerInterface  $successHandler
     * @param AuthenticationFailureHandlerInterface  $failureHandler
     * @param LoggerInterface                        $logger                A LoggerInterface instance
     * @param EventDispatcherInterface               $dispatcher            An EventDispatcherInterface instance
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, array $options = array(), AuthenticationSuccessHandlerInterface $successHandler = null, AuthenticationFailureHandlerInterface $failureHandler = null, LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        if (empty($providerKey)) {
            throw new \InvalidArgumentException('$providerKey must not be empty.');
        }

        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->sessionStrategy = $sessionStrategy;
        $this->providerKey = $providerKey;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->options = array_merge(array(
            'check_path'                     => '/login_check',
            'login_path'                     => '/login',
            'always_use_default_target_path' => false,
            'default_target_path'            => '/',
            'target_path_parameter'          => '_target_path',
            'use_referer'                    => false,
            'failure_path'                   => null,
            'failure_forward'                => false,
        ), $options);
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
        $this->httpUtils = $httpUtils;
    }
    
    /**
     * Set providerMap, called from HybridAuthFactory
     * 
     * @param HybridAuthProviderMap $providerMap
     */
    public function setProviderMap(HybridAuthProviderMap $providerMap)
    {
        $this->providerMap = $providerMap;
    }
    
    /**
     * Set checkPaths, called from HybridAuthFactory
     * 
     * @param array $checkPaths 
     */
    public function setCheckPaths(array $checkPaths)
    {
        $this->checkPaths = $checkPaths;
    }
    
    /**
     * Sets the RememberMeServices implementation to use
     *
     * @param RememberMeServicesInterface $rememberMeServices
     */
    public function setRememberMeServices(RememberMeServicesInterface $rememberMeServices)
    {
        $this->rememberMeServices = $rememberMeServices;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($this->requiresAuthentication($request)) {
            $response = $this->tryAuthentication($event, $request);
            if ($response === null) {
                return ;
            }
        }
        else if (null === $response = $this->tryAutoConnect($event, $request)) {
            return ;
        }
        
        $event->setResponse($response);
    }
    
    /**
     * {@inheritDoc}
     */
    protected function requiresAuthentication(Request $request)
    {
        foreach ($this->checkPaths as $checkPath) {
            if ($this->httpUtils->checkRequestPath($request, $checkPath)) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        // Get a Hybrid_Provider_Adapter by user's authentication to the social network with HybridAuth
        $adapter = $this->providerMap->getProviderAdapterByRequest($request);
        
        // Create a token with the social network authentication
        $token =  $this->generateToken($adapter);
        
        return $this->authenticationManager->authenticate($token);
    }
    
    /**
     * Try to auto connect with hybrid_auth sessions
     * 
     * @param GetResponseEvent $event
     * @param Request $request
     * 
     * @return null|Response
     */
    private function tryAutoConnect(GetResponseEvent $event, Request $request)
    {
        // TODO: PHP auto connec is really usefull ?
        if (!$this->securityContext->getToken()) {
            foreach ($this->providerMap->getConnectedAdapters() as $adapterName) {
                $adapter = $this->providerMap->getProviderAdapterByName($adapterName, true);
                $token =  $this->generateToken($adapter);
                try {
                    $returnValue = $this->authenticationManager->authenticate($token);
                    if (null !== $returnValue) {
                        $this->logger->info('Auto connection with '.$adapter->id.' provider');
                        return $this->onAuthenticated($event, $request, $returnValue);
                    }
                }
                catch (AuthenticationException $e) {
                    return null;
                }
            }
        }
        return null;
    }
    
    /**
     * Try to be authenticate with a specific provider
     * 
     * @param GetResponseEvent $event
     * @param Request $request
     * 
     * @return Response
     * 
     * @throws RuntimeException
     * @throws SessionUnavailableException 
     */
    private function tryAuthentication(GetResponseEvent $event, Request $request)
    {
        if (!$request->hasSession()) {
            throw new \RuntimeException('This authentication method requires a session.');
        }

        try {
            if (!$request->hasPreviousSession()) {
                throw new SessionUnavailableException('Your session has timed-out, or you have disabled cookies.');
            }

            if (null === $returnValue = $this->attemptAuthentication($request)) {
                return null;
            }

            $response = $this->onAuthenticated($event, $request, $returnValue);
        } catch (AuthenticationException $e) {
            $response = $this->onFailure($event, $request, $e);
        }
        return $response;
    }
    
    /**
     * Generate a HybridAuthToken with adapter
     * 
     * @param Hybrid_Provider_Adapter $adapter
     * 
     * @return HybridAuthToken 
     */
    private function generateToken(Hybrid_Provider_Adapter $adapter)
    {
        $adapterToken = $adapter->getAccessToken();
        return new HybridAuthToken($adapterToken['access_token'], $adapter->id);        
    }
    
    /**
     * Return correct Response with the returnValue
     * 
     * @param type $returnValue 
     */
    private function onAuthenticated(GetResponseEvent $event, Request $request, $returnValue)
    {
        if ($returnValue instanceof TokenInterface) {
            $this->sessionStrategy->onAuthentication($request, $returnValue);

            $response = $this->onSuccess($event, $request, $returnValue);
        } elseif ($returnValue instanceof Response) {
            $response = $returnValue;
        } else {
            throw new \RuntimeException('attemptAuthentication() must either return a Response, an implementation of TokenInterface, or null.');
        }
        return $response;
    }
    
    private function onFailure(GetResponseEvent $event, Request $request, AuthenticationException $failed)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('Authentication request failed: %s', $failed->getMessage()));
        }

        $this->securityContext->setToken(null);

        if (null !== $this->failureHandler) {
            return $this->failureHandler->onAuthenticationFailure($request, $failed);
        }

        if (null === $this->options['failure_path']) {
            $this->options['failure_path'] = $this->options['login_path'];
        }

        if ($this->options['failure_forward']) {
            if (null !== $this->logger) {
                $this->logger->debug(sprintf('Forwarding to %s', $this->options['failure_path']));
            }

            $subRequest = $this->httpUtils->createRequest($request, $this->options['failure_path']);
            $subRequest->attributes->set(SecurityContextInterface::AUTHENTICATION_ERROR, $failed);

            return $event->getKernel()->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }

        if (null !== $this->logger) {
            $this->logger->debug(sprintf('Redirecting to %s', $this->options['failure_path']));
        }

        $request->getSession()->set(SecurityContextInterface::AUTHENTICATION_ERROR, $failed);

        return $this->httpUtils->createRedirectResponse($request, $this->options['failure_path']);
    }

    private function onSuccess(GetResponseEvent $event, Request $request, TokenInterface $token)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('User "%s" has been authenticated successfully', $token->getUsername()));
        }

        $this->securityContext->setToken($token);

        $session = $request->getSession();
        $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        $session->remove(SecurityContextInterface::LAST_USERNAME);

        if (null !== $this->dispatcher) {
            $loginEvent = new InteractiveLoginEvent($request, $token);
            $this->dispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
        }

        if (null !== $this->successHandler) {
            $response = $this->successHandler->onAuthenticationSuccess($request, $token);
        } else {
            $response = $this->httpUtils->createRedirectResponse($request, $this->determineTargetUrl($request));
        }

        if (null !== $this->rememberMeServices) {
            $this->rememberMeServices->loginSuccess($request, $response, $token);
        }

        return $response;
    }

    /**
     * Builds the target URL according to the defined options.
     *
     * @param Request $request
     *
     * @return string
     */
    private function determineTargetUrl(Request $request)
    {
        if ($this->options['always_use_default_target_path']) {
            return $this->options['default_target_path'];
        }

        if ($targetUrl = $request->get($this->options['target_path_parameter'], null, true)) {
            return $targetUrl;
        }

        $session = $request->getSession();
        if ($targetUrl = $session->get('_security.target_path')) {
            $session->remove('_security.target_path');

            return $targetUrl;
        }

        if ($this->options['use_referer'] && $targetUrl = $request->headers->get('Referer')) {
            return $targetUrl;
        }

        return $this->options['default_target_path'];
    }
}

?>
