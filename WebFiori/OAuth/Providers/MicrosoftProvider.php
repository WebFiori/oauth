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
 */
class MicrosoftProvider extends AbstractProvider {
    /**
     * Get the authorization URL.
     */
    public function getAuthorizationUrl(): string {
        return 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';
    }

    /**
     * Get default scopes.
     */
    public function getDefaultScopes(): array {
        return ['openid', 'profile', 'email'];
    }

    /**
     * Get the token URL.
     */
    public function getTokenUrl(): string {
        return 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
    }

    /**
     * Get the user info URL.
     */
    public function getUserInfoUrl(): string {
        return 'https://graph.microsoft.com/v1.0/me';
    }
}
