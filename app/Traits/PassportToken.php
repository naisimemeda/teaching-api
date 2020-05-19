<?php

namespace App\Traits;

use DateTime;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Bridge\AccessToken;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\Bridge\Client;
use Laravel\Passport\Bridge\RefreshToken;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Passport;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Str as FacadeStr;

trait PassportToken
{
    protected $identifier;

    /**
     * @param int $length
     * @return string
     */
    private function generateUniqueIdentifier($length = 40)
    {
        try {
            $Identifier = FacadeStr::random($length);
            $this->identifier = $Identifier;
            return $Identifier;
        } catch (\TypeError $e) {
            throw OAuthServerException::serverError('An unexpected error has occurred');
        } catch (\Error $e) {
            throw OAuthServerException::serverError('An unexpected error has occurred');
        } catch (\Exception $e) {
            throw OAuthServerException::serverError('Could not generate a random string');
        }
    }

    /**
     * @param AccessTokenEntityInterface $accessToken
     * @return RefreshToken|RefreshTokenEntityInterface
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    private function issueRefreshToken(AccessTokenEntityInterface $accessToken)
    {
        $maxGenerationAttempts = 10;
        $refreshTokenRepository = app(RefreshTokenRepository::class);

        $refreshToken = $refreshTokenRepository->getNewRefreshToken();
        $refreshToken->setExpiryDateTime((new \DateTime())->add(Passport::refreshTokensExpireIn()));
        $refreshToken->setAccessToken($accessToken);

        while ($maxGenerationAttempts-- > 0) {
            $refreshToken->setIdentifier($this->generateUniqueIdentifier());
            try {
                $refreshTokenRepository->persistNewRefreshToken($refreshToken);

                return $refreshToken;
            } catch (UniqueTokenIdentifierConstraintViolationException $e) {
                if ($maxGenerationAttempts === 0) {
                    throw $e;
                }
            }
        }
    }

    /**
     * @param Model $user
     * @param $clientId
     * @return array
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    protected function createPassportTokenByUser(Model $user, $clientId)
    {
        $accessToken = new AccessToken($user->id);
        $accessToken->setIdentifier($this->generateUniqueIdentifier());
        $accessToken->setClient(new Client($clientId, null, null));
        $accessToken->setExpiryDateTime((new DateTime())->add(Passport::tokensExpireIn()));

        $accessTokenRepository = new AccessTokenRepository(new TokenRepository(), new Dispatcher());
        $accessTokenRepository->persistNewAccessToken($accessToken);
        $refreshToken = $this->issueRefreshToken($accessToken);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }

    /**
     * @param $accessToken
     * @param $refreshToken
     * @return BearerTokenResponse|ResponseInterface
     */
    protected function sendBearerTokenResponse($accessToken, $refreshToken)
    {
        $response = new BearerTokenResponse();
        $response->setAccessToken($accessToken);
        $response->setRefreshToken($refreshToken);

        $privateKey = new CryptKey('file://'.Passport::keyPath('oauth-private.key'), null, false);

        $response->setPrivateKey($privateKey);
        $response->setEncryptionKey(app('encrypter')->getKey());

        return $response->generateHttpResponse(new Response);
    }

    /**
     * @param Model $user
     * @param $clientId
     * @param $output
     * @param string $provider
     * @return array
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    protected function getBearerTokenByUser(Model $user, int $clientId, $output, string $provider): array
    {
        $passportToken = $this->createPassportTokenByUser($user, $clientId);

        $this->identifier = collect(Arr::first($passportToken))->first();

        $bearerToken = $this->sendBearerTokenResponse($passportToken['access_token'], $passportToken['refresh_token']);

        if (! $output) {
            $bearerToken = json_decode($bearerToken->getBody()->__toString(), true);
        }

        $this->setTokenProvider($this->identifier, $provider, $user, $clientId);

        return $bearerToken;
    }

    /**
     * @param string $identifier
     * @param string $provider
     * @param Model $user
     * @param int $clientId
     */
    protected function setTokenProvider(string $identifier, string $provider, Model $user, int $clientId)
    {
        $now = now();

        // 为这个 令牌 添加 provider
        DB::table('oauth_access_token_providers')->insert([
            'oauth_access_token_id' => $identifier,
            'provider' => $provider,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
