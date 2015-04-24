<?php

namespace SLLH\HybridAuthBundle\Security\Http\Logout;

use SLLH\HybridAuthBundle\Security\Http\HybridAuthProviderMap;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

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
        $response->headers->setCookie(new Cookie('sllh_hybridauth_logout', true, 0, '/', $request->getHost(), false));
    }
}

?>
