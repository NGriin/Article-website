<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;


class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{

    use TargetPathTrait;

    protected $httpUtils;
    private $csrfTokenManager;
    private $userProvider;
    private $hasher;

    public function __construct(HttpUtils $httpUtils, CsrfTokenManagerInterface $csrfTokenManager, UserProvider $userProvider, UserPasswordHasherInterface $hasher)
    {
        $this->httpUtils = $httpUtils;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->userProvider = $userProvider;
        $this->hasher = $hasher;

    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->httpUtils->generateUri($request, 'app_login');
    }

    public function supports(Request $request): bool
    {
        return $request->isMethod('POST') && $this->httpUtils->checkRequestPath($request, 'app_login');
    }

    public function authenticate(Request $request): Passport
    {
        $crfToken = $request->request->get('_csrf_token');
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $crfToken))) {
            throw new InvalidCsrfTokenException();
        }
        $badge = new UserBadge($request->request->get('email'), $this->userProvider->getUser(...));
        $passport = new Passport($badge, new CustomCredentials(function ($password, $user) {
            return $this->hasher->isPasswordValid($user, $password);
        }, $request->request->get('password')), [new RememberMeBadge()]);
        return $passport;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($target = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($target);
        }
        return new RedirectResponse(
            $this->httpUtils->generateUri($request, 'app_homepage')
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        return new RedirectResponse($this->httpUtils->generateUri($request, 'app_login'));
    }
}