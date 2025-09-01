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
 */
class OAuth2Client {
    private Provider $provider;
    private TokenStorage $storage;
    private TokenManager $tokenManager;

    /**
     * Create new OAuth2 client.
     */
    public function __construct(Provider $provider, ?TokenStorage $storage = null) {
        $this->provider = $provider;
        $this->storage = $storage ?? new FileTokenStorage();
        $this->tokenManager = new TokenManager($this->storage);
    }

    /**
     * Exchange authorization code for access token.
     */
    public function exchangeCodeForToken(string $code, string $state = null): array {
        $request = new TokenRequest($this->provider);
        $token = $request->exchangeCode($code, $state);
        $this->tokenManager->store('access_token', $token);

        return $token;
    }

    /**
     * Get authorization URL for OAuth flow.
     */
    public function getAuthorizationUrl(array $scopes = []): string {
        $request = new AuthorizationRequest($this->provider);

        return $request->buildUrl($scopes);
    }

    /**
     * Refresh access token using refresh token.
     */
    public function refreshToken(string $refreshToken): array {
        $request = new TokenRequest($this->provider);
        $token = $request->refresh($refreshToken);
        $this->tokenManager->store('access_token', $token);

        return $token;
    }
}
