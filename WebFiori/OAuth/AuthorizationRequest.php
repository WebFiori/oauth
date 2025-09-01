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
 */
class AuthorizationRequest {
    private Provider $provider;

    /**
     * Create new authorization request.
     */
    public function __construct(Provider $provider) {
        $this->provider = $provider;
    }

    /**
     * Build authorization URL with parameters.
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
