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
use WebFiori\OAuth\Storage\FileTokenStorage;
use WebFiori\OAuth\Storage\TokenStorage;

/**
 * OAuth2 client for handling authorization flows.
 * 
 * This class provides a high-level interface for OAuth2 operations including
 * authorization URL generation, token exchange, and token refresh.
 * 
 * @example
 * ```php
 * $provider = new MicrosoftProvider($clientId, $clientSecret, $redirectUri);
 * $client = new OAuth2Client($provider);
 * 
 * // Get authorization URL
 * $authUrl = $client->getAuthorizationUrl(['openid', 'profile']);
 * 
 * // Exchange code for token
 * $token = $client->exchangeCodeForToken($_GET['code']);
 * ```
 */
class OAuth2Client {
    /** @var Provider OAuth2 provider instance */
    private Provider $provider;
    
    /** @var TokenStorage Token storage implementation */
    private TokenStorage $storage;
    
    /** @var TokenManager Token management instance */
    private TokenManager $tokenManager;

    /**
     * Create new OAuth2 client.
     * 
     * @param Provider $provider OAuth2 provider implementation
     * @param TokenStorage|null $storage Token storage implementation (defaults to FileTokenStorage)
     */
    public function __construct(Provider $provider, ?TokenStorage $storage = null) {
        $this->provider = $provider;
        $this->storage = $storage ?? new FileTokenStorage();
        $this->tokenManager = new TokenManager($this->storage);
    }

    /**
     * Exchange authorization code for access token.
     * 
     * @param string $code Authorization code received from OAuth provider
     * @param string|null $state Optional state parameter for CSRF protection
     * @return array Token data including access_token, refresh_token, expires_in, etc.
     * @throws OAuth2Exception When token exchange fails
     */
    public function exchangeCodeForToken(string $code, ?string $state = null): array {
        $request = new TokenRequest($this->provider);
        $token = $request->exchangeCode($code, $state);
        $this->tokenManager->store('access_token', $token);

        return $token;
    }

    /**
     * Get authorization URL for OAuth flow.
     * 
     * @param array<string> $scopes List of OAuth scopes to request (uses provider defaults if empty)
     * @return string Complete authorization URL with parameters
     */
    public function getAuthorizationUrl(array $scopes = []): string {
        $request = new AuthorizationRequest($this->provider);

        return $request->buildUrl($scopes);
    }

    /**
     * Refresh access token using refresh token.
     * 
     * @param string $refreshToken Refresh token from previous authorization
     * @return array New token data including refreshed access_token
     * @throws OAuth2Exception When token refresh fails
     */
    public function refreshToken(string $refreshToken): array {
        $request = new TokenRequest($this->provider);
        $token = $request->refresh($refreshToken);
        $this->tokenManager->store('access_token', $token);

        return $token;
    }
}
