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
 *     'https://yourapp.com/callback'
 * );
 * $client = new OAuth2Client($provider);
 * ```
 * 
 * @see https://docs.microsoft.com/en-us/azure/active-directory/develop/v2-oauth2-auth-code-flow
 */
class MicrosoftProvider extends AbstractProvider {
    /**
     * Get the authorization URL.
     * 
     * @return string Microsoft OAuth2 authorization endpoint
     */
    public function getAuthorizationUrl(): string {
        return 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';
    }

    /**
     * Get default scopes.
     * 
     * @return array<string> Default Microsoft Graph scopes for basic profile access
     */
    public function getDefaultScopes(): array {
        return ['openid', 'profile', 'email'];
    }

    /**
     * Get the token URL.
     * 
     * @return string Microsoft OAuth2 token endpoint
     */
    public function getTokenUrl(): string {
        return 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
    }

    /**
     * Get the user info URL.
     * 
     * @return string Microsoft Graph user profile endpoint
     */
    public function getUserInfoUrl(): string {
        return 'https://graph.microsoft.com/v1.0/me';
    }
}
