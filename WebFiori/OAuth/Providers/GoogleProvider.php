<?php
namespace WebFiori\OAuth\Providers;

/**
 * Google OAuth2 provider implementation.
 * 
 * Provides OAuth2 authentication for Google applications, supporting
 * access to Google APIs including Gmail, Google Drive, Google Cloud Platform,
 * and other Google services.
 * 
 * @example
 * ```php
 * $provider = new GoogleProvider(
 *     'your-google-client-id',
 *     'your-google-client-secret', 
 *     'https://yourapp.com/callback'
 * );
 * 
 * $client = new OAuth2Client($provider);
 * $authUrl = $client->getAuthorizationUrl(['openid', 'email', 'profile']);
 * ```
 * 
 * @see https://developers.google.com/identity/protocols/oauth2
 */
class GoogleProvider extends AbstractProvider {

    /**
     * Get Google OAuth2 authorization endpoint URL.
     * 
     * @return string Google's OAuth2 authorization URL
     */
    public function getAuthorizationUrl(): string {
        return 'https://accounts.google.com/o/oauth2/v2/auth';
    }

    /**
     * Get Google OAuth2 token endpoint URL.
     * 
     * @return string Google's OAuth2 token exchange URL
     */
    public function getTokenUrl(): string {
        return 'https://oauth2.googleapis.com/token';
    }

    /**
     * Get default OAuth2 scopes for Google.
     * 
     * Returns basic user information scopes that allow reading
     * user profile data and email addresses.
     * 
     * @return array<string> Default Google OAuth2 scopes
     */
    public function getDefaultScopes(): array {
        return ['openid', 'email', 'profile'];
    }

    /**
     * Get Google user information API endpoint URL.
     * 
     * @return string Google API endpoint for retrieving authenticated user data
     */
    public function getUserInfoUrl(): string {
        return 'https://www.googleapis.com/oauth2/v2/userinfo';
    }
}
