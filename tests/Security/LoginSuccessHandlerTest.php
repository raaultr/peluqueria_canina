<?php

namespace App\Tests\Security;

use App\Security\LoginSuccessHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\HttpUtils;

final class LoginSuccessHandlerTest extends TestCase
{
    public function testAdminIsRedirectedToDashboard(): void
    {
        $httpUtils = $this->createMock(HttpUtils::class);
        $httpUtils->expects(self::never())->method('createRedirectResponse');

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects(self::once())
            ->method('generate')
            ->with('admin_dashboard')
            ->willReturn('/admin');

        $token = $this->createMock(TokenInterface::class);
        $token->method('getRoleNames')->willReturn(['ROLE_ADMIN']);

        $response = (new LoginSuccessHandler($httpUtils, $urlGenerator))
            ->onAuthenticationSuccess(new Request(), $token);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('/admin', $response->headers->get('Location'));
    }

    public function testNonAdminUsesDefaultSuccessRedirect(): void
    {
        $request = new Request();
        $defaultResponse = new RedirectResponse('/');

        $httpUtils = $this->createMock(HttpUtils::class);
        $httpUtils->expects(self::once())
            ->method('createRedirectResponse')
            ->with($request, '/')
            ->willReturn($defaultResponse);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects(self::never())->method('generate');

        $token = $this->createMock(TokenInterface::class);
        $token->method('getRoleNames')->willReturn(['ROLE_CLIENTE']);

        $response = (new LoginSuccessHandler($httpUtils, $urlGenerator))
            ->onAuthenticationSuccess($request, $token);

        self::assertSame($defaultResponse, $response);
    }
}
