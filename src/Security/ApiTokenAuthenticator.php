<?php

namespace App\Security;

use App\Exception\TokenNotFoundInRequestException;
use App\Repository\ApiTokenRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenExtractorInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    private $userApiProvider;

    public function __construct(UserApiProvider $userApiProvider)
    {
        $this->userApiProvider = $userApiProvider;
    }

    public function supports(Request $request): ?bool
    {
        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get('Authorization');
        if (!$token) {
           throw new TokenNotFoundInRequestException();
        }
        $tokenValue = $this->getTokenValue($token);
        return new SelfValidatingPassport(new UserBadge($tokenValue, $this->userApiProvider->getUser(...)));

    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    protected function getTokenValue($token)
    {
        $tokenParts = explode(' ', $token);
        if(!$tokenValue = $tokenParts[1]??null) {
            throw new TokenNotFoundInRequestException();
        }
      return $tokenValue;
    }
}

//
