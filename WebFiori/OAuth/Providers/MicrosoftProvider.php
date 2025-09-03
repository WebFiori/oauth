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
namespace WebFiori\OAuth\Providers;

/**
 * Microsoft OAuth2 provider.
 * 
 * Implements OAuth2 integration with Microsoft Azure AD / Microsoft Graph.
 * Supports both personal Microsoft accounts and organizational accounts.
 * 
 * @example
 * ```php
 * $provider = new MicrosoftProvider(
 *     'your-client-id',
 *     'your-client-secret', 
 *     'https://yourapp.com/callback',
 *     'your-tenant-id' // Optional, defaults to 'common'
 * );
 * $client = new OAuth2Client($provider);
 * ```
 * 
 * @see https://docs.microsoft.com/en-us/azure/active-directory/develop/v2-oauth2-auth-code-flow
 */
class MicrosoftProvider extends AbstractProvider {
    /** @var string Microsoft tenant ID */
    private string $tenant;

    /**
     * Create new Microsoft provider.
     * 
     * @param string $clientId OAuth2 client identifier
     * @param string $clientSecret OAuth2 client secret
     * @param string $redirectUri OAuth2 redirect URI for callbacks
     * @param string $tenant Microsoft tenant ID (defaults to 'common' for multi-tenant)
     */
    public function __construct(string $clientId, string $clientSecret, string $redirectUri, string $tenant = 'common') {
        parent::__construct($clientId, $clientSecret, $redirectUri);
        $this->tenant = $tenant;
    }

    /**
     * Get the authorization URL.
     * 
     * Authorization URL will have following format:
     * 'https://login.microsoftonline.com/{$this->tenant}/oauth2/v2.0/authorize'
     * 
     * @return string Microsoft OAuth2 authorization endpoint
     */
    public function getAuthorizationUrl(): string {
        return "https://login.microsoftonline.com/{$this->tenant}/oauth2/v2.0/authorize";
    }

    /**
     * Get default scopes.
     * 
     * @see https://learn.microsoft.com/en-us/entra/identity-platform/scopes-oidc
     * 
     * @return array<string> Default Microsoft Graph scopes for basic profile access. The
     * returned array contains the following scopes:
     * - openid: Required scope for OpenID Connect authentication
     * - profile: Required scope for basic profile information
     * - email: Required scope for accessing the user's email address
     */
    public function getDefaultScopes(): array {
        return ['openid', 'profile', 'email'];
    }

    /**
     * Get the token URL.
     * 
     * The returned URL will have following format:
     * 'https://login.microsoftonline.com/{$this->tenant}/oauth2/v2.0/token'
     * 
     * @return string Microsoft OAuth2 token endpoint.
     */
    public function getTokenUrl(): string {
        return "https://login.microsoftonline.com/{$this->tenant}/oauth2/v2.0/token";
    }

    /**
     * Get the user info URL.
     * 
     * @return string Microsoft Graph user profile endpoint. The value of the URL is 
     * 'https://graph.microsoft.com/v1.0/me'
     */
    public function getUserInfoUrl(): string {
        return 'https://graph.microsoft.com/v1.0/me';
    }
}
