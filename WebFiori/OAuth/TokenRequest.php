<?php
/**
 * This file is licensed under MIT License.
 *
 * Copyright (c) 2025 Ibrahim BinAlshikh and Contributors 
 *
 * For more information on the license, please visit:
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 *
 */
namespace WebFiori\OAuth;

use WebFiori\OAuth\Exceptions\OAuth2Exception;
use WebFiori\OAuth\Providers\Provider;

/**
 * Handles OAuth2 token requests.
 * 
 * This class manages HTTP requests to OAuth2 token endpoints for
 * authorization code exchange and token refresh operations.
 * 
 * @example
 * ```php
 * $provider = new MicrosoftProvider($clientId, $clientSecret, $redirectUri);
 * $request = new TokenRequest($provider);
 * $token = $request->exchangeCode($authCode);
 * ```
 */
class TokenRequest {
    /** @var Provider OAuth2 provider instance */
    private Provider $provider;

    /**
     * Create new token request.
     * 
     * @param Provider $provider OAuth2 provider implementation
     */
    public function __construct(Provider $provider) {
        $this->provider = $provider;
    }

    /**
     * Exchange authorization code for access token.
     * 
     * @param string $code Authorization code received from OAuth provider
     * @param string|null $state Optional state parameter for validation
     * @return array Token response containing access_token, refresh_token, expires_in, etc.
     * @throws OAuth2Exception When the token request fails
     */
    public function exchangeCode(string $code, ?string $state = null): array {
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->provider->getClientId(),
            'client_secret' => $this->provider->getClientSecret(),
            'code' => $code,
            'redirect_uri' => $this->provider->getRedirectUri()
        ];

        return $this->makeRequest($params);
    }

    /**
     * Refresh access token using refresh token.
     * 
     * @param string $refreshToken Valid refresh token from previous authorization
     * @return array New token response with refreshed access_token
     * @throws OAuth2Exception When the refresh request fails
     */
    public function refresh(string $refreshToken): array {
        $params = [
            'grant_type' => 'refresh_token',
            'client_id' => $this->provider->getClientId(),
            'client_secret' => $this->provider->getClientSecret(),
            'refresh_token' => $refreshToken
        ];

        return $this->makeRequest($params);
    }

    /**
     * Make HTTP request to token endpoint.
     * 
     * @param array<string, string> $params Request parameters for token endpoint
     * @return array Decoded JSON response from token endpoint
     * @throws OAuth2Exception When HTTP request fails or returns non-200 status
     */
    protected function makeRequest(array $params): array {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->provider->getTokenUrl());
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new OAuth2Exception('Token request failed with HTTP '.$httpCode.': '.$response);
        }

        // Try JSON first, then URL-encoded form data
        $data = json_decode($response, true);
        
        if ($data === null) {
            // Parse URL-encoded response (GitHub format)
            parse_str($response, $data);
            
            if (empty($data)) {
                throw new OAuth2Exception('Invalid response format from token endpoint: '.$response);
            }
        }

        if (isset($data['expires_in'])) {
            $data['expires_at'] = time() + $data['expires_in'];
        }

        return $data;
    }
}
