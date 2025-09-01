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
 */
interface Provider {
    /**
     * Get the authorization URL.
     */
    public function getAuthorizationUrl(): string;

    /**
     * Get the client ID.
     */
    public function getClientId(): string;

    /**
     * Get the client secret.
     */
    public function getClientSecret(): string;

    /**
     * Get default scopes.
     */
    public function getDefaultScopes(): array;

    /**
     * Get the redirect URI.
     */
    public function getRedirectUri(): string;

    /**
     * Get the token URL.
     */
    public function getTokenUrl(): string;

    /**
     * Get the user info URL.
     */
    public function getUserInfoUrl(): string;
}
