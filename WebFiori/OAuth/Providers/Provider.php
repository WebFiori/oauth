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
 * OAuth2 provider interface.
 * 
 * Defines the contract for OAuth2 provider implementations. Each provider
 * must implement methods to return OAuth2 endpoints and configuration.
 * 
 * @example
 * ```php
 * class CustomProvider implements Provider {
 *     public function getAuthorizationUrl(): string {
 *         return 'https://example.com/oauth/authorize';
 *     }
 *     // ... implement other methods
 * }
 * ```
 */
interface Provider {
    /**
     * Get the authorization URL.
     * 
     * @return string OAuth2 authorization endpoint URL
     */
    public function getAuthorizationUrl(): string;

    /**
     * Get the client ID.
     * 
     * @return string OAuth2 client identifier
     */
    public function getClientId(): string;

    /**
     * Get the client secret.
     * 
     * @return string OAuth2 client secret
     */
    public function getClientSecret(): string;

    /**
     * Get default scopes.
     * 
     * @return array<string> List of default OAuth2 scopes for this provider
     */
    public function getDefaultScopes(): array;

    /**
     * Get the redirect URI.
     * 
     * @return string Configured redirect URI for OAuth2 callbacks
     */
    public function getRedirectUri(): string;

    /**
     * Get the token URL.
     * 
     * @return string OAuth2 token endpoint URL for code exchange
     */
    public function getTokenUrl(): string;

    /**
     * Get the user info URL.
     * 
     * @return string API endpoint URL for retrieving user information
     */
    public function getUserInfoUrl(): string;
}
