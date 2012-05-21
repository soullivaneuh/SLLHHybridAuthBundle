<?php

namespace SLLH\HybridAuthBundle\Security\Http\Logout;

use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Cookie;

use SLLH\HybridAuthBundle\Security\Http\HybridAuthProviderMap;

/**
 * HybridAuthLogoutHandler
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class HybridAuthLogoutHandler implements LogoutHandlerInterface
{
    /**
     * @var HybridAuthProviderMap
     */
    private $providerMap;

    public function __construct(HybridAuthProviderMap $providerMap)
    {
        $this->providerMap = $providerMap;
    }
    
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $this->providerMap->getHybridAuth()->logoutAllProviders();
        $response->headers->setCookie(new Cookie('sllh_hybridauth_logout', true, 0, '/', 'localsf2.dowith.fr', false));
    }
}

?>
