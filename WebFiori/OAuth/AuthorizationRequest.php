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

use WebFiori\OAuth\Providers\Provider;

/**
 * Handles OAuth2 authorization requests.
 * 
 * This class builds authorization URLs for OAuth2 flows, including
 * proper parameter encoding and state generation for CSRF protection.
 * 
 * @example
 * ```php
 * $provider = new MicrosoftProvider($clientId, $clientSecret, $redirectUri);
 * $request = new AuthorizationRequest($provider);
 * $authUrl = $request->buildUrl(['openid', 'profile', 'email']);
 * ```
 */
class AuthorizationRequest {
    /** @var Provider OAuth2 provider instance */
    private Provider $provider;

    /**
     * Create new authorization request.
     * 
     * @param Provider $provider OAuth2 provider implementation
     */
    public function __construct(Provider $provider) {
        $this->provider = $provider;
    }

    /**
     * Build authorization URL with parameters.
     * 
     * Generates a complete OAuth2 authorization URL with all required parameters
     * including client_id, redirect_uri, scopes, and a random state for CSRF protection.
     * 
     * @param array<string> $scopes List of OAuth scopes to request (uses provider defaults if empty)
     * @return string Complete authorization URL ready for redirect
     */
    public function buildUrl(array $scopes = []): string {
        $params = [
            'response_type' => 'code',
            'client_id' => $this->provider->getClientId(),
            'redirect_uri' => $this->provider->getRedirectUri(),
            'scope' => implode(' ', $scopes ?: $this->provider->getDefaultScopes()),
            'state' => bin2hex(random_bytes(16))
        ];

        return $this->provider->getAuthorizationUrl().'?'.http_build_query($params);
    }
}
