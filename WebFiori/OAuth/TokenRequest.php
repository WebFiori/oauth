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
 */
class TokenRequest {
    private Provider $provider;

    /**
     * Create new token request.
     */
    public function __construct(Provider $provider) {
        $this->provider = $provider;
    }

    /**
     * Exchange authorization code for access token.
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
     */
    private function makeRequest(array $params): array {
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
            throw new OAuth2Exception('Token request failed with HTTP '.$httpCode);
        }

        $data = json_decode($response, true);

        if (isset($data['expires_in'])) {
            $data['expires_at'] = time() + $data['expires_in'];
        }

        return $data;
    }
}
